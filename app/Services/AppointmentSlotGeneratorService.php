<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Classes;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentSlotGeneratorService
{
    /**
     * Genera bloques de slots para un rango de horas, respetando breaks
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @param string $startTime Hora inicio en formato HH:MM
     * @param string $endTime Hora fin en formato HH:MM
     * @param int $slotDurationMinutes Duración del slot en minutos
     * @param string|null $breakStartTime Hora inicio del break en formato HH:MM
     * @param string|null $breakEndTime Hora fin del break en formato HH:MM
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    public static function generateTimeSlots(
        string $date,
        string $startTime,
        string $endTime,
        int $slotDurationMinutes,
        ?string $breakStartTime = null,
        ?string $breakEndTime = null
    ): Collection {
        $slots = new Collection();

        try {
            $sessionStart = Carbon::createFromFormat('Y-m-d H:i', "$date $startTime");
            $sessionEnd = Carbon::createFromFormat('Y-m-d H:i', "$date $endTime");

            // Parse break times if provided
            $breakStart = null;
            $breakEnd = null;
            if ($breakStartTime && $breakEndTime) {
                $breakStart = Carbon::createFromFormat('Y-m-d H:i', "$date $breakStartTime");
                $breakEnd = Carbon::createFromFormat('Y-m-d H:i', "$date $breakEndTime");
            }

            $currentStart = $sessionStart->copy();

            while ($currentStart < $sessionEnd) {
                $currentEnd = $currentStart->copy()->addMinutes($slotDurationMinutes);

                // Skip slot if it overlaps with break
                if ($breakStart && $breakEnd) {
                    // Check if slot overlaps with break period
                    if ($currentStart < $breakEnd && $currentEnd > $breakStart) {
                        // Move past the break
                        $currentStart = $breakEnd->copy();
                        continue;
                    }
                }

                // Only add slot if it doesn't exceed session end
                if ($currentEnd <= $sessionEnd) {
                    $slots->push([
                        'start' => $currentStart->copy(),
                        'end' => $currentEnd->copy(),
                    ]);
                }

                $currentStart = $currentEnd;
            }
        } catch (\Exception $e) {
            // Return empty collection on parse error
            return new Collection();
        }

        return $slots;
    }

    /**
     * Obtiene profesores únicos que imparten en los grados especificados
     *
     * @param array<string> $grades Códigos de grados (ej: ['10', '11', '12'])
     * @return Collection<int, int> IDs de profesores únicos
     */
    public static function getTeacherIdsByGrades(array $grades): Collection
    {
        return Classes::whereIn('grado', $grades)
            ->distinct('id')
            ->pluck('id')
            ->filter(fn($id) => !empty($id));
    }
}
