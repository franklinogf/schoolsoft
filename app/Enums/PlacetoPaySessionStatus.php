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
            self::OK => __('Aprobado'),
            self::FAILED => __('Falló'),
            self::APPROVED => __('Aprobado'),
            self::APPROVED_PARTIAL => __('Aprobado parcialmente'),
            self::REJECTED => __('Rechazado'),
            self::PENDING => __('Pendiente'),
            self::PENDING_VALIDATION => __('Pendiente de validación'),
            self::REFUNDED => __('Reembolsado'),
            self::ERROR => __('Error'),
            self::UNKNOWN => __('Desconocido'),
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
