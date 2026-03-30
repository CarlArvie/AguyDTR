<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard');
    }

    public function data(Request $request): JsonResponse
    {
        $targetHours = (float) ($request->user()->target_hours ?? 486);

        $entries = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('work_date')
            ->get();

        $totalMinutes = 0;
        $entriesPayload = [];
        $weeklyMinutes = [];

        foreach ($entries as $entry) {
            $entryMinutes = $this->calculateEntryMinutes($entry);
            $totalMinutes += $entryMinutes;

            if ($entry->work_date) {
                $weekStart = $entry->work_date->copy()->startOfWeek(Carbon::MONDAY);
                $weekEnd = $entry->work_date->copy()->endOfWeek(Carbon::SUNDAY);
                $weekKey = $weekStart->toDateString();

                if (!isset($weeklyMinutes[$weekKey])) {
                    $weeklyMinutes[$weekKey] = [
                        'start' => $weekStart,
                        'end' => $weekEnd,
                        'minutes' => 0,
                    ];
                }

                $weeklyMinutes[$weekKey]['minutes'] += $entryMinutes;
            }

            $entriesPayload[] = [
                'id' => $entry->id,
                'work_date' => optional($entry->work_date)->format('M d, Y'),
                'work_date_raw' => optional($entry->work_date)->toDateString(),
                'morning_in' => $this->formatTimeForDisplay($entry->morning_in),
                'morning_out' => $this->formatTimeForDisplay($entry->morning_out),
                'afternoon_in' => $this->formatTimeForDisplay($entry->afternoon_in),
                'afternoon_out' => $this->formatTimeForDisplay($entry->afternoon_out),
                'daily_hours' => number_format($entryMinutes / 60, 2),
            ];
        }

        $totalHours = $totalMinutes / 60;
        $progressPercent = $targetHours > 0 ? min(100, max(0, ($totalHours / $targetHours) * 100)) : 0;
        ksort($weeklyMinutes);

        $weeklyTotalsPayload = [];
        $weekNumber = 1;
        foreach ($weeklyMinutes as $weekData) {
            $weeklyTotalsPayload[] = [
                'week_label' => 'Week ' . $weekNumber++,
                'range' => $weekData['start']->format('M d') . ' - ' . $weekData['end']->format('M d, Y'),
                'hours' => number_format($weekData['minutes'] / 60, 2, '.', ''),
            ];
        }

        return response()->json([
            'total_hours' => number_format($totalHours, 2, '.', ''),
            'target_hours' => number_format($targetHours, 2, '.', ''),
            'progress_percent' => number_format($progressPercent, 2, '.', ''),
            'entries' => $entriesPayload,
            'weekly_totals' => $weeklyTotalsPayload,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedDate = $request->query('date', now()->toDateString());
        $selectedDateCarbon = Carbon::parse($selectedDate);

        $entry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', $selectedDate)
            ->first();

        $previousEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '<', $selectedDateCarbon)
            ->orderByDesc('work_date')
            ->first();
        $nextEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '>', $selectedDateCarbon)
            ->orderBy('work_date')
            ->first();

        return view('add-time-out', [
            'entry' => $entry,
            'selectedDate' => $selectedDate,
            'previousEntry' => $previousEntry,
            'nextEntry' => $nextEntry,
        ]);
    }

    public function entryData(Request $request): JsonResponse
    {
        $dateInput = $request->query('date', now()->toDateString());

        try {
            $selectedDate = Carbon::parse($dateInput)->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid date.',
            ], 422);
        }

        $selectedDateCarbon = Carbon::parse($selectedDate);

        $entry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', $selectedDate)
            ->first();

        $previousEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '<', $selectedDateCarbon)
            ->orderByDesc('work_date')
            ->first();
        $nextEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '>', $selectedDateCarbon)
            ->orderBy('work_date')
            ->first();

        return response()->json([
            'work_date' => $selectedDate,
            'entry' => [
                'id' => $entry?->id,
                'morning_in' => $entry?->morning_in,
                'morning_out' => $entry?->morning_out,
                'afternoon_in' => $entry?->afternoon_in,
                'afternoon_out' => $entry?->afternoon_out,
                'activity_description' => $entry?->activity_description,
            ],
            'previous_date' => $previousEntry?->work_date?->toDateString(),
            'next_date' => $nextEntry?->work_date?->toDateString(),
        ]);
    }

    public function show(Request $request, TimeEntry $timeEntry): View
    {
        if ($timeEntry->user_id !== $request->user()->id) {
            abort(403);
        }

        $dailyHours = number_format($this->calculateEntryMinutes($timeEntry) / 60, 2);
        $previousEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '<', $timeEntry->work_date)
            ->orderByDesc('work_date')
            ->first();
        $nextEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '>', $timeEntry->work_date)
            ->orderBy('work_date')
            ->first();

        return view('time-entry-view', [
            'entry' => $timeEntry,
            'dailyHours' => $dailyHours,
            'previousEntry' => $previousEntry,
            'nextEntry' => $nextEntry,
        ]);
    }

    public function showData(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        if ($timeEntry->user_id !== $request->user()->id) {
            abort(403);
        }

        $dailyHours = number_format($this->calculateEntryMinutes($timeEntry) / 60, 2);
        $previousEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '<', $timeEntry->work_date)
            ->orderByDesc('work_date')
            ->first();
        $nextEntry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', '>', $timeEntry->work_date)
            ->orderBy('work_date')
            ->first();

        return response()->json([
            'id' => $timeEntry->id,
            'work_date' => $timeEntry->work_date?->toDateString(),
            'work_date_display' => $timeEntry->work_date?->format('F d, Y'),
            'morning_in' => $this->formatTimeForDisplay($timeEntry->morning_in),
            'morning_out' => $this->formatTimeForDisplay($timeEntry->morning_out),
            'afternoon_in' => $this->formatTimeForDisplay($timeEntry->afternoon_in),
            'afternoon_out' => $this->formatTimeForDisplay($timeEntry->afternoon_out),
            'daily_hours' => $dailyHours,
            'activity_description' => $timeEntry->activity_description ?: '-',
            'previous_id' => $previousEntry?->id,
            'next_id' => $nextEntry?->id,
            'edit_url' => route('time-entries.create', ['date' => $timeEntry->work_date?->toDateString()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'work_date' => ['required', 'date'],
            'morning_in' => ['nullable', 'string', 'max:20'],
            'morning_out' => ['nullable', 'string', 'max:20'],
            'afternoon_in' => ['nullable', 'string', 'max:20'],
            'afternoon_out' => ['nullable', 'string', 'max:20'],
            'activity_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $normalized = [];
            foreach (['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'] as $field) {
                $raw = $request->input($field);
                $normalized[$field] = $this->normalizeTime($raw);

                if (!empty($raw) && !$normalized[$field]) {
                    $validator->errors()->add($field, 'Invalid time format. Use hh:mm AM/PM.');
                }
            }

            $this->validateTimePair($validator, $normalized['morning_in'], $normalized['morning_out'], 'morning');
            $this->validateTimePair($validator, $normalized['afternoon_in'], $normalized['afternoon_out'], 'afternoon');

            $hasAnyTime = collect([
                $request->input('morning_in'),
                $request->input('morning_out'),
                $request->input('afternoon_in'),
                $request->input('afternoon_out'),
            ])->contains(fn ($value) => !empty($value));

            if (!$hasAnyTime) {
                $validator->errors()->add('time', 'At least one time value is required.');
            }
        });

        $validated = $validator->validate();

        $normalizedTimes = [
            'morning_in' => $this->normalizeTime($validated['morning_in'] ?? null),
            'morning_out' => $this->normalizeTime($validated['morning_out'] ?? null),
            'afternoon_in' => $this->normalizeTime($validated['afternoon_in'] ?? null),
            'afternoon_out' => $this->normalizeTime($validated['afternoon_out'] ?? null),
        ];

        TimeEntry::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'work_date' => $validated['work_date'],
            ],
            [
                'morning_in' => $normalizedTimes['morning_in'],
                'morning_out' => $normalizedTimes['morning_out'],
                'afternoon_in' => $normalizedTimes['afternoon_in'],
                'afternoon_out' => $normalizedTimes['afternoon_out'],
                'activity_description' => $validated['activity_description'] ?? null,
            ]
        );

        return redirect()
            ->route('time-entries.create', ['date' => $validated['work_date']])
            ->with('status', 'Time entry saved.');
    }

    public function destroy(Request $request, TimeEntry $timeEntry): RedirectResponse
    {
        if ($timeEntry->user_id !== $request->user()->id) {
            abort(403);
        }

        $timeEntry->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Time entry deleted.');
    }

    private function validateTimePair($validator, ?string $start, ?string $end, string $label): void
    {
        if (!empty($start) && empty($end)) {
            $validator->errors()->add("{$label}_out", ucfirst($label) . ' out time is required when in time is provided.');
            return;
        }

        if (empty($start) && !empty($end)) {
            $validator->errors()->add("{$label}_in", ucfirst($label) . ' in time is required when out time is provided.');
            return;
        }

        if (empty($start) || empty($end)) {
            return;
        }

        $startMinutes = $this->timeToMinutes($start);
        $endMinutes = $this->timeToMinutes($end);

        if ($startMinutes === null || $endMinutes === null || $endMinutes <= $startMinutes) {
            $validator->errors()->add("{$label}_out", ucfirst($label) . ' out time must be later than in time.');
        }
    }

    private function calculateEntryMinutes(TimeEntry $entry): int
    {
        return $this->minutesBetween($entry->morning_in, $entry->morning_out)
            + $this->minutesBetween($entry->afternoon_in, $entry->afternoon_out);
    }

    private function minutesBetween(?string $start, ?string $end): int
    {
        $startMinutes = $this->timeToMinutes($start);
        $endMinutes = $this->timeToMinutes($end);

        if ($startMinutes === null || $endMinutes === null) {
            return 0;
        }

        $minutes = $endMinutes - $startMinutes;

        return $minutes > 0 ? $minutes : 0;
    }

    private function normalizeTime(?string $time): ?string
    {
        $minutes = $this->timeToMinutes($time);

        if ($minutes === null) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }

    private function formatTimeForDisplay(?string $time): string
    {
        $minutes = $this->timeToMinutes($time);

        if ($minutes === null) {
            return '-';
        }

        $hours24 = intdiv($minutes, 60);
        $mins = $minutes % 60;
        $ampm = $hours24 >= 12 ? 'PM' : 'AM';
        $hours12 = $hours24 % 12;
        if ($hours12 === 0) {
            $hours12 = 12;
        }

        return sprintf('%d:%02d %s', $hours12, $mins, $ampm);
    }

    private function timeToMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }

        $value = trim((string) $time);

        if ($value === '') {
            return null;
        }

        if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2})(?:\.\d+)?)?\s*([AaPp][Mm])?$/', $value, $matches)) {
            return null;
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if ($minutes < 0 || $minutes > 59) {
            return null;
        }

        $meridiem = $matches[4] ?? '';

        if ($meridiem !== '') {
            $meridiem = strtoupper($meridiem);

            if ($hours < 1 || $hours > 12) {
                return null;
            }

            if ($hours === 12) {
                $hours = $meridiem === 'AM' ? 0 : 12;
            } elseif ($meridiem === 'PM') {
                $hours += 12;
            }
        } else {
            if ($hours < 0 || $hours > 23) {
                return null;
            }
        }

        return ($hours * 60) + $minutes;
    }
}
