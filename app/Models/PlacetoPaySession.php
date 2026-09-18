<?php

namespace App\Models;

use App\Enums\PlacetoPaySessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Tracks every PlacetoPay Web Checkout session created by this tenant, so a
 * requestId can always be found back from the internal `reference` even if
 * the buyer never returns through `returnUrl`.
 *
 * @property int $id
 * @property string $reference
 * @property int|null $request_id
 * @property PlacetoPaySessionStatus $status
 * @property float $amount
 * @property string $currency
 * @property string|null $payable_type
 * @property string|null $payable_id
 * @property string|null $process_url
 * @property array<string, mixed>|null $last_response
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PlacetoPaySession extends Model
{
    protected $table = 'placetopay_sessions';

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PlacetoPaySessionStatus::class,
            'amount' => 'float',
            'last_response' => 'array',
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Persist a new session attempt as PENDING, before calling PlacetoPay.
     * This guarantees the reference/amount is on record even if the remote
     * call to PlacetoPay never comes back (timeout, network error, etc).
     */
    public static function startPending(string $reference, float $amount, string $currency, ?string $payableType = null, ?string $payableId = null): self
    {
        return self::create([
            'reference' => $reference,
            'status' => PlacetoPaySessionStatus::PENDING,
            'amount' => $amount,
            'currency' => $currency,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
        ]);
    }

    /**
     * Record the requestId/processUrl returned by PlacetoPay after creating
     * the session, or the failure reason if it didn't succeed.
     */
    public function markCreated(?int $requestId, ?string $processUrl, PlacetoPaySessionStatus $status, array $rawResponse): self
    {
        $this->update([
            'request_id' => $requestId,
            'process_url' => $processUrl,
            'status' => $status,
            'last_response' => $rawResponse,
        ]);

        return $this;
    }

    /**
     * Update the status/raw response after querying PlacetoPay.
     */
    public function markStatus(PlacetoPaySessionStatus $status, array $rawResponse): self
    {
        $this->update([
            'status' => $status,
            'last_response' => $rawResponse,
        ]);

        return $this;
    }

    public static function findByReference(string $reference): ?self
    {
        return self::where('reference', $reference)->first();
    }

    public static function findByRequestId(int $requestId): ?self
    {
        return self::where('request_id', $requestId)->first();
    }
}
