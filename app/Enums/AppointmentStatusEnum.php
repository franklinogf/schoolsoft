<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case BOOKED = 'booked';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function getLabel(): string
    {
        return match ($this) {
            self::BOOKED => __('Reservado'),
            self::DONE => __('Completado'),
            self::CANCELLED => __('Cancelado'),
            self::NO_SHOW => __('No Asistió'),
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::BOOKED => 'The appointment is scheduled.',
            self::DONE => 'The appointment has been completed successfully.',
            self::CANCELLED => 'The appointment was cancelled.',
            self::NO_SHOW => 'The user did not attend the scheduled appointment.',
        };
    }

    public function isFinalStatus(): bool
    {
        return in_array($this, [self::DONE, self::CANCELLED, self::NO_SHOW]);
    }
}
