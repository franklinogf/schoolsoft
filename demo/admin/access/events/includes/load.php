<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\CalendarEvent;

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$events = CalendarEvent::query()
    ->when($start, fn($query) => $query->where('start_at', '>=', $start))
    ->when($end, fn($query) => $query->where('end_at', '<=', $end))
    ->get()
    ->map(fn(CalendarEvent $event): array =>
    [
        'id' => $event->id,
        'title' => $event->title,
        'start' => $event->start_at->toISOString(),
        'end' => $event->end_at->toISOString(),
        'color' => $event->color,
        'extendedProps' => [
            'description' => $event->description,
            'location' => $event->location,
        ],
    ])
    ->toArray();

echo  json_encode($events);
