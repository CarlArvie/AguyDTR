<x-layouts.app title="Time Entry Details">
    @php
        $formatTime = fn ($time) => $time ? substr((string) $time, 0, 5) : '-';
    @endphp

    <div class="space-y-6 rounded-xl border border-orange-500 bg-[rgb(38,38,38)] p-6 shadow-sm fade-up">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-orange-600">Time Entry Details</h1>
                <p class="text-sm text-orange-400">{{ $entry->work_date->format('F d, Y') }}</p>
            </div>
            <a
                href="{{ route('dashboard') }}"
                class="rounded-md border border-orange-500 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-gray-100"
            >
                Back
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-orange-500 p-4">
                <h2 class="mb-3 text-sm font-semibold text-orange-600">Morning</h2>
                <p class="text-sm text-orange-300">In: <span class="text-orange-100">{{ $formatTime($entry->morning_in) }}</span></p>
                <p class="text-sm text-orange-300">Out: <span class="text-orange-100">{{ $formatTime($entry->morning_out) }}</span></p>
            </div>

            <div class="rounded-lg border border-orange-500 p-4">
                <h2 class="mb-3 text-sm font-semibold text-orange-600">Afternoon</h2>
                <p class="text-sm text-orange-300">In: <span class="text-orange-100">{{ $formatTime($entry->afternoon_in) }}</span></p>
                <p class="text-sm text-orange-300">Out: <span class="text-orange-100">{{ $formatTime($entry->afternoon_out) }}</span></p>
            </div>
        </div>

        <div class="rounded-lg border border-orange-500 p-4">
            <h2 class="mb-2 text-sm font-semibold text-orange-600">Total Daily Hours</h2>
            <p class="text-sm text-orange-100">{{ $dailyHours }}</p>
        </div>

        <div class="rounded-lg border border-orange-500 p-4">
            <h2 class="mb-2 text-sm font-semibold text-orange-600">Activities / Description</h2>
            <p class="whitespace-pre-wrap text-sm text-orange-100">{{ $entry->activity_description ?: '-' }}</p>
        </div>
    </div>
</x-layouts.app>
