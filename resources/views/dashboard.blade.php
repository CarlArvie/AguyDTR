<x-layouts.app title="Dashboard">
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-4">
                <p class="text-sm text-orange-500">Total Hours</p>
                <p id="total-hours" class="mt-1 text-2xl font-semibold text-orange-700">-</p>
            </div>

            <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-4">
                <p class="text-sm text-orange-500">Target Hours</p>
                <p id="target-hours" class="mt-1 text-2xl font-semibold text-orange-700">-</p>
            </div>
        </div>

        <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-4">
            <div class="mb-2 flex items-center justify-between text-sm">
                <p class="text-orange-600">Progress</p>
                <p id="progress-percent" class="text-orange-600">0.00%</p>
            </div>
            <div class="h-3 w-full rounded-full bg-orange-200">
                <div id="progress-bar" class="h-3 rounded-full bg-orange-600" style="width: 0%;"></div>
            </div>
            <p id="progress-summary" class="mt-2 text-xs text-orange-500">- / - hours</p>
        </div>

        <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-4">
            <h2 class="mb-3 text-base font-semibold text-orange-700">Weekly Totals</h2>
            <p class="mb-3 text-xs text-orange-400">Grouped by Monday to Sunday.</p>
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-orange-500/40 text-left text-orange-500">
                            <th class="px-3 py-2">Week</th>
                            <th class="px-3 py-2">Range</th>
                            <th class="px-3 py-2 text-right">Hours</th>
                        </tr>
                    </thead>
                    <tbody id="weekly-totals-body">
                        <tr>
                            <td colspan="3" class="px-3 py-3 text-center text-orange-400">
                                Loading weekly totals...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-orange-500 bg-[rgb(38,38,38)] p-4">
            <h2 class="mb-3 text-base font-semibold text-orange-700">All Time Entries</h2>

            <div id="db-loading" class="flex items-center gap-3 rounded-md bg-black/20 px-4 py-3 text-sm text-orange-400">
                <span class="h-5 w-5 animate-spin rounded-full border-2 border-orange-500 border-t-transparent"></span>
                Fetching data from database...
            </div>

            <p id="db-error" class="hidden rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                Failed to fetch dashboard data. Please refresh.
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-orange-600">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Morning In</th>
                            <th class="px-3 py-2">Morning Out</th>
                            <th class="px-3 py-2">Afternoon In</th>
                            <th class="px-3 py-2">Afternoon Out</th>
                            <th class="px-3 py-2">Daily Hours</th>
                            <th class="px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="entries-body">
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-orange-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const loading = document.getElementById('db-loading');
            const error = document.getElementById('db-error');
            const entriesBody = document.getElementById('entries-body');
            const totalHoursNode = document.getElementById('total-hours');
            const targetHoursNode = document.getElementById('target-hours');
            const progressPercentNode = document.getElementById('progress-percent');
            const progressSummaryNode = document.getElementById('progress-summary');
            const progressBar = document.getElementById('progress-bar');
            const weeklyTotalsBodyNode = document.getElementById('weekly-totals-body');

            const escapeHtml = (value) => String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const hideLoading = () => {
                if (loading) {
                    loading.classList.add('hidden');
                }
            };

            fetch(@json(route('dashboard.data')), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed request');
                    }
                    return response.json();
                })
                .then((data) => {
                    totalHoursNode.textContent = data.total_hours;
                    targetHoursNode.textContent = data.target_hours;
                    progressPercentNode.textContent = `${data.progress_percent}%`;
                    progressSummaryNode.textContent = `${data.total_hours} / ${data.target_hours} hours`;
                    progressBar.style.width = `${data.progress_percent}%`;

                    if (!Array.isArray(data.weekly_totals) || data.weekly_totals.length === 0) {
                        weeklyTotalsBodyNode.innerHTML = `
                            <tr>
                                <td colspan="3" class="px-2 py-3 text-center text-orange-400">
                                    No weekly totals yet.
                                </td>
                            </tr>
                        `;
                    } else {
                        weeklyTotalsBodyNode.innerHTML = data.weekly_totals.map((week) => `
                            <tr class="border-b border-orange-500/20 text-orange-400">
                                <td class="px-2 py-2 font-medium text-orange-500">${escapeHtml(week.week_label ?? '-')}</td>
                                <td class="px-2 py-2">${escapeHtml(week.range ?? '-')}</td>
                                <td class="px-2 py-2 text-right text-orange-300">${escapeHtml(week.hours ?? '0.00')}</td>
                            </tr>
                        `).join('');
                    }

                    if (!Array.isArray(data.entries) || data.entries.length === 0) {
                        entriesBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-orange-500">No time entries found.</td>
                            </tr>
                        `;
                        return;
                    }

                    entriesBody.innerHTML = data.entries.map((entry) => `
                        <tr class="border-b border-orange-500 text-orange-600">
                            <td class="px-3 py-2">${escapeHtml(entry.work_date ?? '-')}</td>
                            <td class="px-3 py-2">${escapeHtml(entry.morning_in ?? '-')}</td>
                            <td class="px-3 py-2">${escapeHtml(entry.morning_out ?? '-')}</td>
                            <td class="px-3 py-2">${escapeHtml(entry.afternoon_in ?? '-')}</td>
                            <td class="px-3 py-2">${escapeHtml(entry.afternoon_out ?? '-')}</td>
                            <td class="px-3 py-2">${escapeHtml(entry.daily_hours ?? '0.00')}</td>
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <a href="/time-entry/${entry.id}" class="rounded border border-orange-300 px-2 py-1 text-xs text-orange-600 hover:bg-orange-50">View</a>
                                    <a href="/time-entry?date=${encodeURIComponent(entry.work_date_raw ?? '')}" class="rounded border border-gray-300 px-2 py-1 text-xs text-orange-600 hover:bg-gray-100">Edit</a>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(() => {
                    if (error) {
                        error.classList.remove('hidden');
                    }
                    weeklyTotalsBodyNode.innerHTML = `
                        <tr>
                            <td colspan="3" class="px-2 py-3 text-center text-red-300">
                                Unable to load weekly totals.
                            </td>
                        </tr>
                    `;
                    entriesBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-red-400">Unable to load entries.</td>
                        </tr>
                    `;
                })
                .finally(hideLoading);
        })();
    </script>
</x-layouts.app>
