<?php

namespace App\Services;

use App\Enums\PlacetoPaySessionStatus;
use App\Models\PlacetoPaySession;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
use Dnetix\Redirection\Message\Notification;
use Dnetix\Redirection\Message\RedirectInformation;
use Dnetix\Redirection\PlacetoPay;

/**
 * Thin wrapper around the PlacetoPay Web Checkout SDK (dnetix/redirection)
 * that also keeps a local record (`placetopay_sessions`) of every session,
 * so the requestId can be found back from our own `reference` even if the
 * buyer never returns through `returnUrl`.
 *
 * Credentials are per-tenant, read from `/config/services.php` under the
 * `placetopay` key (see school_config()).
 */
class PlacetoPayCheckout
{
    /**
     * Max length PlacetoPay accepts for `payment.reference`.
     */
    public const int REFERENCE_MAX_LENGTH = 32;

    /**
     * How long the buyer has to complete the checkout (recommended 10-30).
     */
    public const int EXPIRATION_MINUTES = 30;

    private PlacetoPay $client;

    /**
     * Build a unique payment reference no longer than REFERENCE_MAX_LENGTH.
     * The parts are joined with `_` into a descriptive head (e.g. `STO`,
     * store prefix, account id) that gets truncated to fit, while the
     * unique tail (timestamp + random digits) is always kept intact.
     * Retries until the reference is not used by any previous session
     * (pending, approved or rejected).
     */
    public static function generateReference(string|int ...$parts): string
    {
        do {
            $tail = date('ymdHis') . random_int(100, 999);
            $head = substr(implode('_', $parts), 0, self::REFERENCE_MAX_LENGTH - strlen($tail) - 1);
            $reference = $head === '' ? $tail : $head . '_' . $tail;
        } while (PlacetoPaySession::referenceExists($reference));

        return $reference;
    }

    public function __construct()
    {
        $this->client = new PlacetoPay([
            'login' => school_config('services.placetopay.login'),
            'tranKey' => school_config('services.placetopay.tran_key'),
            'baseUrl' => school_config('services.placetopay.base_url'),
        ]);
    }

    /**
     * Create a new checkout session, persist it locally as PENDING first,
     * then create it in PlacetoPay and record the requestId/processUrl (or
     * the failure) back onto the same row.
     *
     * @param array{
     *  reference:string,
     *  description:string,
     *  currency?:string,
     *  amount:float,
     *  returnUrl:string,
     *  accountId:string|int,
     *  buyerEmail:string,
     *  buyerName:string,
     *  buyerSurname:string,
     *  buyerMobile:string,
     *  payableType?:string,
     *  payableId?:string,
     *  skipResult?:bool
     * } $data
     * @param array<int, array{keyword:string, value:string|int|string[]|int[], displayOn:'none'|'payment'|'receipt'|'both'|'approved'}> $fields
     *  Extra fields; `CustomerAccountNumber` (the parent account) is always added.
     * @param array<int, array{sku:string|int, name:string, qty:int, price:float, category?:'physical'|'digital', tax?:float}> $items
     *  Optional purchase lines shown on the checkout page (`payment.items`).
     */
    public function createSession(array $data, array $fields = [], array $items = []): PlacetoPaySession
    {
        $currency = $data['currency'] ?? 'USD';

        $session = PlacetoPaySession::startPending(
            $data['reference'],
            $data['amount'],
            $currency,
            $data['payableType'] ?? null,
            $data['payableId'] ?? null,
            (string) $data['accountId'],
            $data['description'],
        );

        $request = [
            'locale' => 'es_PR',
            'buyer' => [
                'name' => $data['buyerName'],
                'surname' => $data['buyerSurname'],
                'email' => $data['buyerEmail'],
                'mobile' => $data['buyerMobile'],
            ],
            'payment' => [
                'reference' => $data['reference'],
                'description' => $data['description'],
                'amount' => [
                    'currency' => $currency,
                    'total' => $data['amount'],
                ],
            ],
            'fields' => [
                [
                    'keyword' => 'CustomerAccountNumber',
                    'value' => (string) $data['accountId'],
                    'displayOn' => 'both',
                ],
                ...$fields,
            ],
            'expiration' => date('c', strtotime('+' . self::EXPIRATION_MINUTES . ' minutes')),
            'returnUrl' => $data['returnUrl'],
            'ipAddress' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'skipResult' => $data['skipResult'] ?? false,
        ];

        if (! empty($items)) {
            $request['payment']['items'] = array_map(fn (array $item) => [
                'sku' => (string) $item['sku'],
                'name' => $item['name'],
                'category' => $item['category'] ?? 'physical',
                'qty' => (int) $item['qty'],
                'price' => (float) $item['price'],
                'tax' => (float) ($item['tax'] ?? 0),
            ], $items);
        }

        try {
            $response = $this->client->request($request);
        } catch (PlacetoPayException $e) {
            $session->markCreated(null, null, PlacetoPaySessionStatus::FAILED, ['error' => $e->getMessage()]);
            throw $e;
        }

        // The status returned here is the status of the *create request*
        // (OK/FAILED), not of the payment. A created session stays PENDING
        // until the buyer pays, so it can't be mistaken for an approval.
        $created = $response->isSuccessful() && $response->processUrl();

        $session->markCreated(
            $response->requestId() !== '' ? (int) $response->requestId() : null,
            $response->processUrl() ?: null,
            $created ? PlacetoPaySessionStatus::PENDING : PlacetoPaySessionStatus::FAILED,
            $response->toArray(),
        );

        return $session;
    }

    /**
     * Query the current status of a previously created session by requestId
     * and sync the local record with the latest status/raw response.
     *
     * @throws PlacetoPayException
     */
    public function querySession(int $requestId): RedirectInformation
    {
        $info = $this->client->query($requestId);

        $session = PlacetoPaySession::findByRequestId($requestId);
        $session?->markStatus(
            PlacetoPaySessionStatus::fromApiStatus($info->status()->status()),
            $info->toArray(),
        );

        return $info;
    }

    /**
     * Query the current status using our own internal reference instead of
     * PlacetoPay's requestId — useful when the buyer never returned through
     * `returnUrl` and all you have is the reference you generated.
     *
     * @throws PlacetoPayException
     */
    public function querySessionByReference(string $reference): ?RedirectInformation
    {
        $session = PlacetoPaySession::findByReference($reference);

        if (! $session || ! $session->request_id) {
            return null;
        }

        return $this->querySession($session->request_id);
    }

    /**
     * Parse a webhook notification body sent by PlacetoPay.
     */
    public function readNotification(array $payload): Notification
    {
        return $this->client->readNotification($payload);
    }

    /**
     * Validate the notification signature. PlacetoPay signs with
     * sha1(requestId + status + date + tranKey) by default, or sha256 when
     * the signature comes prefixed with `sha256:`.
     */
    public function isValidNotification(Notification $notification): bool
    {
        $signature = $notification->signature();
        $seed = $notification->requestId()
            . $notification->status()->status()
            . $notification->status()->date()
            . school_config('services.placetopay.tran_key');

        if (str_starts_with($signature, 'sha256:')) {
            return hash_equals(hash('sha256', $seed), substr($signature, 7));
        }

        return hash_equals(sha1($seed), $signature);
    }
}
