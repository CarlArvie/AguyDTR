<x-layouts.app title="Add Time In/Out">
    @php
        $entry = $entry ?? null;
        $selectedDate = old('work_date', $selectedDate ?? now()->toDateString());
        $formatTime = fn ($value) => $value ? substr((string) $value, 0, 5) : '';
    @endphp

    <div class="rounded-xl bg-[rgb(38,38,38)] p-6 shadow-sm fade-up">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-orange-600">Add Time In/Out</h1>
            <p class="text-sm text-orange-400">Record morning and afternoon in/out times.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('time'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('time') }}
            </div>
        @endif

        <form method="POST" action="{{ route('time-entries.store') }}" class="space-y-6">
            @csrf

            <div class="">
                <label for="work_date" class="mb-1 block text-sm font-medium text-orange-600">Date</label>
                <input
                    id="work_date"
                    name="work_date"
                    type="date"
                    value="{{ $selectedDate }}"
                    class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                >
                @error('work_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-orange-500 p-4">
                    <h2 class="mb-3 text-sm font-semibold text-orange-600">Morning</h2>
                    <div class="space-y-3">
                        <div>
                            <label for="morning_in" class="mb-1 block text-sm text-orange-600">In</label>
                            <input
                                id="morning_in"
                                name="morning_in"
                                type="time"
                                value="{{ old('morning_in', $formatTime($entry?->morning_in)) }}"
                                class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            >
                            @error('morning_in')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="morning_out" class="mb-1 block text-sm text-orange-600">Out</label>
                            <input
                                id="morning_out"
                                name="morning_out"
                                type="time"
                                value="{{ old('morning_out', $formatTime($entry?->morning_out)) }}"
                                class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            >
                            @error('morning_out')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-orange-600 p-4">
                    <h2 class="mb-3 text-sm font-semibold text-orange-600">Afternoon</h2>
                    <div class="space-y-3">
                        <div>
                            <label for="afternoon_in" class="mb-1 block text-sm text-orange-600">In</label>
                            <input
                                id="afternoon_in"
                                name="afternoon_in"
                                type="time"
                                value="{{ old('afternoon_in', $formatTime($entry?->afternoon_in)) }}"
                                class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            >
                            @error('afternoon_in')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="afternoon_out" class="mb-1 block text-sm text-orange-600">Out</label>
                            <input
                                id="afternoon_out"
                                name="afternoon_out"
                                type="time"
                                value="{{ old('afternoon_out', $formatTime($entry?->afternoon_out)) }}"
                                class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                            >
                            @error('afternoon_out')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="activity_description" class="mb-1 block text-sm font-medium text-orange-600">Activities / Description</label>
                <textarea
                    id="activity_description"
                    name="activity_description"
                    rows="4"
                    placeholder="Describe your tasks or activities for this day..."
                    class="w-full rounded-md border border-orange-500 px-3 py-2 text-sm text-orange-600 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                >{{ old('activity_description', $entry?->activity_description) }}</textarea>
                @error('activity_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-md border border-orange-500 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-gray-100"
                    data-show-loader="true"
                >
                    Back
                </a>
                <button
                    type="submit"
                    class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700"
                >
                    Save Time Entry
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
