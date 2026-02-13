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

        foreach ($entries as $entry) {
            $entryMinutes = $this->calculateEntryMinutes($entry);
            $totalMinutes += $entryMinutes;

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

        return response()->json([
            'total_hours' => number_format($totalHours, 2, '.', ''),
            'target_hours' => number_format($targetHours, 2, '.', ''),
            'progress_percent' => number_format($progressPercent, 2, '.', ''),
            'entries' => $entriesPayload,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedDate = $request->query('date', now()->toDateString());

        $entry = TimeEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', $selectedDate)
            ->first();

        return view('add-time-out', [
            'entry' => $entry,
            'selectedDate' => $selectedDate,
        ]);
    }

    public function show(Request $request, TimeEntry $timeEntry): View
    {
        if ($timeEntry->user_id !== $request->user()->id) {
            abort(403);
        }

        $dailyHours = number_format($this->calculateEntryMinutes($timeEntry) / 60, 2);

        return view('time-entry-view', [
            'entry' => $timeEntry,
            'dailyHours' => $dailyHours,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'work_date' => ['required', 'date'],
            'morning_in' => ['nullable', 'date_format:H:i'],
            'morning_out' => ['nullable', 'date_format:H:i'],
            'afternoon_in' => ['nullable', 'date_format:H:i'],
            'afternoon_out' => ['nullable', 'date_format:H:i'],
            'activity_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateTimePair($validator, $request->input('morning_in'), $request->input('morning_out'), 'morning');
            $this->validateTimePair($validator, $request->input('afternoon_in'), $request->input('afternoon_out'), 'afternoon');

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

        TimeEntry::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'work_date' => $validated['work_date'],
            ],
            [
                'morning_in' => $validated['morning_in'] ?? null,
                'morning_out' => $validated['morning_out'] ?? null,
                'afternoon_in' => $validated['afternoon_in'] ?? null,
                'afternoon_out' => $validated['afternoon_out'] ?? null,
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

        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        if ($endTime->lessThanOrEqualTo($startTime)) {
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
        if (!$start || !$end) {
            return 0;
        }

        $startTs = strtotime($start);
        $endTs = strtotime($end);

        if ($startTs === false || $endTs === false || $endTs <= $startTs) {
            return 0;
        }

        return (int) (($endTs - $startTs) / 60);
    }

    private function formatTimeForDisplay(?string $time): string
    {
        return $time ? substr((string) $time, 0, 5) : '-';
    }
}
