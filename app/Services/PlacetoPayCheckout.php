<?php

namespace App\Services;

use App\Enums\PlacetoPaySessionStatus;
use App\Models\PlacetoPaySession;
use Dnetix\Redirection\Exceptions\PlacetoPayException;
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
    private PlacetoPay $client;

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
     *  buyerEmail?:string,
     *  buyerName?:string,
     *  buyerSurname?:string,
     *  payableType?:string,
     *  payableId?:string,
     * } $data
     */
    public function createSession(array $data): PlacetoPaySession
    {
        $currency = $data['currency'] ?? 'USD';

        $session = PlacetoPaySession::startPending(
            $data['reference'],
            $data['amount'],
            $currency,
            $data['payableType'] ?? null,
            $data['payableId'] ?? null,
        );

        $request = [
            'locale' => 'es_PR',
            'payment' => [
                'reference' => $data['reference'],
                'description' => $data['description'],
                'amount' => [
                    'currency' => $currency,
                    'total' => $data['amount'],
                ],
            ],
            'expiration' => date('c', strtotime('+1 day')),
            'returnUrl' => $data['returnUrl'],
            'ipAddress' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        ];

        if (! empty($data['buyerEmail'])) {
            $request['buyer'] = [
                'name' => $data['buyerName'] ?? '',
                'surname' => $data['buyerSurname'] ?? '',
                'email' => $data['buyerEmail'],
            ];
        }

        try {
            $response = $this->client->request($request);
        } catch (PlacetoPayException $e) {
            $session->markCreated(null, null, PlacetoPaySessionStatus::FAILED, ['error' => $e->getMessage()]);
            throw $e;
        }

        $session->markCreated(
            $response->requestId() !== '' ? (int) $response->requestId() : null,
            $response->processUrl() ?: null,
            PlacetoPaySessionStatus::fromApiStatus($response->status()->status()),
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
}
