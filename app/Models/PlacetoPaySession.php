<?php

namespace App\Models;

use App\Enums\PlacetoPaySessionStatus;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string|null $description
 * @property string|null $payable_type
 * @property string|null $payable_id
 * @property string|null $account_id
 * @property string|null $process_url
 * @property array<string, mixed>|null $last_response
 * @property \Carbon\Carbon|null $applied_at  When the payment was credited to its payable
 * @property \Carbon\Carbon|null $refunded_at When a refund/reversal was applied to its payable
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
            'applied_at' => 'datetime',
            'refunded_at' => 'datetime',
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
    public static function startPending(
        string $reference,
        float $amount,
        string $currency,
        ?string $payableType = null,
        ?string $payableId = null,
        ?string $accountId = null,
        ?string $description = null,
    ): self {
        return self::create([
            'reference' => $reference,
            'status' => PlacetoPaySessionStatus::PENDING,
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
            'account_id' => $accountId,
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

    /**
     * Sessions that reached PlacetoPay and are still waiting for a final
     * status (card pending, ACH in validation, buyer still on the checkout).
     */
    protected function scopePending(Builder $query): void
    {
        $query->whereNotNull('request_id')
            ->whereIn('status', [PlacetoPaySessionStatus::PENDING, PlacetoPaySessionStatus::PENDING_VALIDATION]);
    }

    protected function scopeForAccount(Builder $query, int|string $accountId): void
    {
        $query->where('account_id', $accountId);
    }

    /**
     * Payment history: every session that actually reached PlacetoPay.
     */
    protected function scopeHistory(Builder $query): void
    {
        $query->whereNotNull('request_id')->orderByDesc('created_at');
    }

    public static function referenceExists(string $reference): bool
    {
        return self::where('reference', $reference)->exists();
    }

    public static function findByReference(string $reference): ?self
    {
        return self::where('reference', $reference)->first();
    }

    public static function findByRequestId(int $requestId): ?self
    {
        return self::where('request_id', $requestId)->first();
    }

    /**
     * Message PlacetoPay returned with the latest status, if any.
     */
    public function statusMessage(): ?string
    {
        return $this->last_response['status']['message'] ?? $this->last_response['error'] ?? null;
    }
}
