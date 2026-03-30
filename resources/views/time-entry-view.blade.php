<x-layouts.app title="Time Entry Details">
    @php
        $formatTime = function ($time) {
            if (!$time) {
                return '-';
            }

            try {
                return \Carbon\Carbon::parse($time)->format('g:i A');
            } catch (\Exception $e) {
                return '-';
            }
        };
    @endphp

    <div class="flex items-center gap-3 md:gap-4">
        <div class="shrink-0">
            <a
                id="btn-prev"
                href="{{ $previousEntry ? route('time-entries.show', $previousEntry) : '#' }}"
                data-id="{{ $previousEntry?->id }}"
                aria-disabled="{{ $previousEntry ? 'false' : 'true' }}"
                class="flex items-center justify-center rounded-full border border-orange-500 bg-[rgb(38,38,38)] px-3 py-2 text-sm font-medium text-orange-600 shadow-sm hover:bg-gray-100 {{ $previousEntry ? '' : 'pointer-events-none opacity-50 cursor-not-allowed' }}"
                aria-label="Previous entry"
            >
                &larr;
            </a>
        </div>

        <div
            id="time-entry-card"
            data-show-endpoint-base="{{ url('/time-entry') }}"
            data-current-id="{{ $entry->id }}"
            class="w-full space-y-6 rounded-xl border border-orange-500 bg-[rgb(38,38,38)] p-6 shadow-sm fade-up"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-orange-600">Time Entry Details</h1>
                    <p id="entry-date" class="text-sm text-orange-400">{{ $entry->work_date->format('F d, Y') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        id="edit-entry-link"
                        href="{{ route('time-entries.create', ['date' => $entry->work_date->toDateString()]) }}"
                        class="rounded-md border border-orange-500 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-gray-100"
                    >
                        Edit
                    </a>

                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-md border border-orange-500 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-gray-100"
                    >
                        Back
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-orange-500 p-4">
                    <h2 class="mb-3 text-sm font-semibold text-orange-600">Morning</h2>
                    <p class="text-sm text-orange-300">In: <span id="morning-in" class="text-orange-100">{{ $formatTime($entry->morning_in) }}</span></p>
                    <p class="text-sm text-orange-300">Out: <span id="morning-out" class="text-orange-100">{{ $formatTime($entry->morning_out) }}</span></p>
                </div>

                <div class="rounded-lg border border-orange-500 p-4">
                    <h2 class="mb-3 text-sm font-semibold text-orange-600">Afternoon</h2>
                    <p class="text-sm text-orange-300">In: <span id="afternoon-in" class="text-orange-100">{{ $formatTime($entry->afternoon_in) }}</span></p>
                    <p class="text-sm text-orange-300">Out: <span id="afternoon-out" class="text-orange-100">{{ $formatTime($entry->afternoon_out) }}</span></p>
                </div>
            </div>

            <div class="rounded-lg border border-orange-500 p-4">
                <h2 class="mb-2 text-sm font-semibold text-orange-600">Total Daily Hours</h2>
                <p id="daily-hours" class="text-sm text-orange-100">{{ $dailyHours }}</p>
            </div>

            <div class="rounded-lg border border-orange-500 p-4">
                <h2 class="mb-2 text-sm font-semibold text-orange-600">Activities / Description</h2>
                <p id="activity-description" class="whitespace-pre-wrap text-sm text-orange-100">{{ $entry->activity_description ?: '-' }}</p>
            </div>
        </div>

        <div class="shrink-0">
            <a
                id="btn-next"
                href="{{ $nextEntry ? route('time-entries.show', $nextEntry) : '#' }}"
                data-id="{{ $nextEntry?->id }}"
                aria-disabled="{{ $nextEntry ? 'false' : 'true' }}"
                class="flex items-center justify-center rounded-full border border-orange-500 bg-[rgb(38,38,38)] px-3 py-2 text-sm font-medium text-orange-600 shadow-sm hover:bg-gray-100 {{ $nextEntry ? '' : 'pointer-events-none opacity-50 cursor-not-allowed' }}"
                aria-label="Next entry"
            >
                &rarr;
            </a>
        </div>
    </div>

    <script>
        (() => {
            const card = document.getElementById('time-entry-card');
            if (!card) return;

            const baseUrl = card.dataset.showEndpointBase;
            const prevButton = document.getElementById('btn-prev');
            const nextButton = document.getElementById('btn-next');
            const entryDate = document.getElementById('entry-date');
            const morningIn = document.getElementById('morning-in');
            const morningOut = document.getElementById('morning-out');
            const afternoonIn = document.getElementById('afternoon-in');
            const afternoonOut = document.getElementById('afternoon-out');
            const dailyHours = document.getElementById('daily-hours');
            const activity = document.getElementById('activity-description');
            const editLink = document.getElementById('edit-entry-link');

            let currentId = Number(card.dataset.currentId || 0);

            const setButtonState = (button, targetId) => {
                if (!button) return;

                if (targetId) {
                    button.dataset.id = targetId;
                    button.href = `${baseUrl}/${targetId}`;
                    button.setAttribute('aria-disabled', 'false');
                    button.classList.remove('pointer-events-none', 'opacity-50', 'cursor-not-allowed');
                } else {
                    button.dataset.id = '';
                    button.href = '#';
                    button.setAttribute('aria-disabled', 'true');
                    button.classList.add('pointer-events-none', 'opacity-50', 'cursor-not-allowed');
                }
            };

            const setLoading = (isLoading) => {
                card.classList.toggle('opacity-70', isLoading);
                card.classList.toggle('pointer-events-none', isLoading);
            };

            const updateView = (data) => {
                entryDate.textContent = data.work_date_display || '-';
                morningIn.textContent = data.morning_in || '-';
                morningOut.textContent = data.morning_out || '-';
                afternoonIn.textContent = data.afternoon_in || '-';
                afternoonOut.textContent = data.afternoon_out || '-';
                dailyHours.textContent = data.daily_hours || '-';
                activity.textContent = data.activity_description || '-';

                if (editLink && data.edit_url) {
                    editLink.href = data.edit_url;
                }

                currentId = data.id || currentId;
                setButtonState(prevButton, data.previous_id);
                setButtonState(nextButton, data.next_id);

                if (data.id) {
                    window.history.replaceState({}, '', `${baseUrl}/${data.id}`);
                }
            };

            const fetchEntry = (id) => {
                if (!baseUrl || !id) return;

                setLoading(true);

                fetch(`${baseUrl}/${id}/data`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch entry data.');
                        }
                        return response.json();
                    })
                    .then((data) => updateView(data))
                    .catch((error) => console.error(error))
                    .finally(() => setLoading(false));
            };

            prevButton?.addEventListener('click', (event) => {
                const id = prevButton.dataset.id;
                if (!id) return;
                event.preventDefault();
                fetchEntry(id);
            });

            nextButton?.addEventListener('click', (event) => {
                const id = nextButton.dataset.id;
                if (!id) return;
                event.preventDefault();
                fetchEntry(id);
            });
        })();
    </script>
</x-layouts.app>
