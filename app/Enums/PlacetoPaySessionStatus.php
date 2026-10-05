<?php

namespace App\Enums;

/**
 * Status values for a `placetopay_sessions` row.
 *
 * Mirrors the status codes returned by the PlacetoPay Checkout API
 * (see `Dnetix\Redirection\Entities\Status::ST_*`), plus `FAILED` which we
 * set locally when the request to create the session never got a response.
 */
enum PlacetoPaySessionStatus: string
{
    case OK = 'ok';
    case FAILED = 'failed';
    case APPROVED = 'approved';
    case APPROVED_PARTIAL = 'approved_partial';
    case REJECTED = 'rejected';
    case PENDING = 'pending';
    case PENDING_VALIDATION = 'pending_validation';
    case REFUNDED = 'refunded';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';

    /**
     * PlacetoPay's API returns status codes in UPPERCASE (e.g. `APPROVED`).
     * Use this instead of `tryFrom()` when mapping their raw status string.
     */
    public static function fromApiStatus(string $status): self
    {
        return self::tryFrom(strtolower($status)) ?? self::UNKNOWN;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OK => __('placetopay.status.approved'),
            self::FAILED => __('placetopay.status.failed'),
            self::APPROVED => __('placetopay.status.approved'),
            self::APPROVED_PARTIAL => __('placetopay.status.approved_partial'),
            self::REJECTED => __('placetopay.status.rejected'),
            self::PENDING => __('placetopay.status.pending'),
            self::PENDING_VALIDATION => __('placetopay.status.pending_validation'),
            self::REFUNDED => __('placetopay.status.refunded'),
            self::ERROR => __('Error'),
            self::UNKNOWN => __('placetopay.status.unknown'),
        };
    }

    /**
     * CSS class used in test.php to color the status badge.
     */
    public function getCssClass(): string
    {
        return match ($this) {
            self::OK, self::APPROVED, self::APPROVED_PARTIAL, self::REFUNDED => 'ok',
            self::PENDING, self::PENDING_VALIDATION => 'pending',
            default => 'other',
        };
    }

    /**
     * Bootstrap badge class used by the parents payment history/result pages.
     */
    public function getBadgeClass(): string
    {
        return match ($this) {
            self::OK, self::APPROVED, self::APPROVED_PARTIAL => 'badge-success bg-success',
            self::PENDING, self::PENDING_VALIDATION => 'badge-warning bg-warning text-dark',
            self::REFUNDED => 'badge-info bg-info',
            default => 'badge-danger bg-danger',
        };
    }

    public function isApproved(): bool
    {
        return in_array($this, [self::OK, self::APPROVED, self::APPROVED_PARTIAL], true);
    }

    public function isPending(): bool
    {
        return in_array($this, [self::PENDING, self::PENDING_VALIDATION], true);
    }

    /**
     * The checkout ended without the buyer paying (cancelled, rejected, the
     * session could not be created), so the payable can be discarded.
     */
    public function isRejected(): bool
    {
        return in_array($this, [self::REJECTED, self::FAILED, self::ERROR], true);
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::OK,
            self::APPROVED,
            self::APPROVED_PARTIAL,
            self::REJECTED,
            self::REFUNDED,
            self::FAILED,
            self::ERROR,
        ]);
    }
}
