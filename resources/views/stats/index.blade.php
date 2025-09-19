<x-layouts.app :title="__($isAdmin ? 'Statistics' : 'My Statistics')">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ $isAdmin ? 'Statistics' : 'My Statistics' }}</h1>

        @php
            $formatLabel = static function ($label): string {
                $label = is_string($label) ? $label : (string) $label;
                $label = str_replace(['_', '-'], ' ', $label);

                return ucwords($label);
            };

            $asChartData = static function ($data, string $label = 'Shifts') use ($formatLabel): array {
                $collection = collect($data ?? []);

                return [
                    'labels' => $collection->keys()->map(fn ($key) => $formatLabel($key))->values()->toArray(),
                    'datasets' => [
                        [
                            'label' => $label,
                            'data' => $collection->values()->map(fn ($value) => (float) $value)->values()->toArray(),
                        ],
                    ],
                ];
            };

            $chartConfigs = [
                'chart-by-type' => [
                    'type' => 'bar',
                    'title' => 'Shifts by Type',
                    'indexAxis' => 'y',
                    'data' => $asChartData($byType),
                ],
                'chart-by-location' => [
                    'type' => 'doughnut',
                    'title' => 'Shifts by Location',
                    'data' => $asChartData($byLocation),
                ],
                'chart-next30-location' => [
                    'type' => 'bar',
                    'title' => 'Next 30 Days by Location',
                    'data' => $asChartData($next30ByLocation),
                ],
            ];

            if ($isAdmin) {
                $chartConfigs['chart-office-coverage'] = [
                    'type' => 'doughnut',
                    'title' => 'Office Coverage (Next 30 Days)',
                    'data' => [
                        'labels' => ['Days with Key Holder', 'Days without Key Holder'],
                        'datasets' => [
                            [
                                'label' => 'Days',
                                'data' => [(float) $officeDaysWithKey, (float) $officeDaysWithoutKey],
                            ],
                        ],
                    ],
                ];

                $chartConfigs['chart-shifts-per-team'] = [
                    'type' => 'bar',
                    'title' => 'Shifts per Team (Next 30 Days)',
                    'data' => $asChartData(collect($perTeamNext30 ?? [])->mapWithKeys(fn ($count, $key) => [strtoupper((string) $key) => $count])),
                ];
            }
        @endphp

        <!-- Summary cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Shifts</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalShifts) }}</div>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalHours, 1) }}</div>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Hours / Shift</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($avgHoursPerShift, 2) }}</div>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Shifts (Next 7 Days)</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($upcomingWeekShifts) }}</div>
            </div>
        </div>

        <!-- Splits -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">By Type</h2>
                <div class="mt-3 flex flex-col gap-4 lg:flex-row">
                    <div class="lg:flex-1">
                        <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                            @foreach($byType as $type => $count)
                                <li class="flex justify-between"><span class="capitalize">{{ $type }}</span><span>{{ number_format($count) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3">
                            <div class="relative h-48">
                                <canvas id="chart-by-type" role="img" aria-label="Bar chart showing shifts by type" aria-describedby="chart-by-type-description" class="h-full w-full"></canvas>
                            </div>
                        </div>
                        <p id="chart-by-type-description" class="sr-only">Bar chart showing shift counts by type. Values are also listed in the adjacent table.</p>
                        <p data-chart-fallback="chart-by-type" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">Chart unavailable because there is no shift data for this section.</p>
                    </div>
                </div>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">By Location</h2>
                <div class="mt-3 flex flex-col gap-4 lg:flex-row">
                    <div class="lg:flex-1">
                        <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                            @foreach($byLocation as $loc => $count)
                                <li class="flex justify-between"><span class="capitalize">{{ $loc }}</span><span>{{ number_format($count) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3">
                            <div class="relative h-48">
                                <canvas id="chart-by-location" role="img" aria-label="Doughnut chart showing shifts by location" aria-describedby="chart-by-location-description" class="h-full w-full"></canvas>
                            </div>
                        </div>
                        <p id="chart-by-location-description" class="sr-only">Doughnut chart showing the proportion of shifts by location. Refer to the list for exact counts.</p>
                        <p data-chart-fallback="chart-by-location" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">Chart unavailable because there is no shift data for this section.</p>
                    </div>
                </div>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Next 30 Days: Location Split</h2>
                <div class="mt-3 flex flex-col gap-4 lg:flex-row">
                    <div class="lg:flex-1">
                        <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                            @forelse($next30ByLocation as $loc => $count)
                                <li class="flex justify-between"><span class="capitalize">{{ $loc }}</span><span>{{ number_format($count) }}</span></li>
                            @empty
                                <li class="text-gray-500 dark:text-gray-400">No upcoming shifts</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3">
                            <div class="relative h-48">
                                <canvas id="chart-next30-location" role="img" aria-label="Bar chart showing next 30 days of shifts by location" aria-describedby="chart-next30-location-description" class="h-full w-full"></canvas>
                            </div>
                        </div>
                        <p id="chart-next30-location-description" class="sr-only">Bar chart showing how upcoming shifts over the next 30 days are distributed by location. The list includes exact values.</p>
                        <p data-chart-fallback="chart-next30-location" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">Chart unavailable because there is no upcoming shift data for this section.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office coverage (admin only) -->
        @if($isAdmin)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Office Coverage (Next 30 Days)</h2>
                    <div class="mt-3 flex flex-col gap-6 lg:flex-row">
                        <div class="space-y-4 text-gray-800 dark:text-gray-200 lg:flex-1">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Days with Key Holder</div>
                                <div class="text-2xl font-semibold">{{ number_format($officeDaysWithKey) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Days without Key Holder</div>
                                <div class="text-2xl font-semibold">{{ number_format($officeDaysWithoutKey) }}</div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Average daily shifts (next 30 days): <span class="font-medium">{{ number_format($avgDailyShifts, 1) }}</span></div>
                        </div>
                        <div class="lg:w-1/2">
                            <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3">
                                <div class="relative h-48">
                                    <canvas id="chart-office-coverage" role="img" aria-label="Doughnut chart showing office coverage with and without a key holder" aria-describedby="chart-office-coverage-description" class="h-full w-full"></canvas>
                                </div>
                            </div>
                            <p id="chart-office-coverage-description" class="sr-only">Doughnut chart showing the number of upcoming office days with and without a key holder available.</p>
                            <p data-chart-fallback="chart-office-coverage" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">Chart unavailable because there is no upcoming coverage data for this section.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Shifts per Team (Next 30 Days)</h2>
                    <div class="mt-3 flex flex-col gap-4 lg:flex-row">
                        <div class="lg:flex-1">
                            <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                                @forelse($perTeamNext30 as $teamKey => $count)
                                    <li class="flex justify-between"><span class="uppercase">{{ $teamKey }}</span><span>{{ number_format($count) }}</span></li>
                                @empty
                                    <li class="text-gray-500 dark:text-gray-400">No upcoming shifts</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="lg:w-1/2">
                            <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-3">
                                <div class="relative h-48">
                                    <canvas id="chart-shifts-per-team" role="img" aria-label="Bar chart showing upcoming shifts by team" aria-describedby="chart-shifts-per-team-description" class="h-full w-full"></canvas>
                                </div>
                            </div>
                            <p id="chart-shifts-per-team-description" class="sr-only">Bar chart showing how upcoming shifts over the next 30 days are distributed per team. Exact counts are listed alongside the chart.</p>
                            <p data-chart-fallback="chart-shifts-per-team" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">Chart unavailable because there is no upcoming shift data for this section.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Top users -->
        @if($isAdmin)
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Top Users by Hours (Next 30 Days)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="py-2 pr-4">User</th>
                                <th class="py-2 pr-4">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse($topUsersNext30 as $row)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="py-2 pr-4">{{ $row->name }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $row->hours, 1) }}</td>
                                </tr>
                            @empty
                                <tr><td class="py-2 pr-4 text-gray-500 dark:text-gray-400" colspan="2">No upcoming shifts</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @push('scripts')
            <script>
                window.statsChartConfigs = @json($chartConfigs);
            </script>
            @vite('resources/js/stats.js')
        @endpush
    </div>
</x-layouts.app>
