<x-layouts.app :title="__($isAdmin ? 'Statistics' : 'My Statistics')">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ $isAdmin ? 'Statistics' : 'My Statistics' }}</h1>

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
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">By Type</h2>
                <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                    @foreach($byType as $type => $count)
                        <li class="flex justify-between"><span class="capitalize">{{ $type }}</span><span>{{ number_format($count) }}</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">By Location</h2>
                <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                    @foreach($byLocation as $loc => $count)
                        <li class="flex justify-between"><span class="capitalize">{{ $loc }}</span><span>{{ number_format($count) }}</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Next 30 Days: Location Split</h2>
                <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                    @forelse($next30ByLocation as $loc => $count)
                        <li class="flex justify-between"><span class="capitalize">{{ $loc }}</span><span>{{ number_format($count) }}</span></li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-400">No upcoming shifts</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Office coverage (admin only) -->
        @if($isAdmin)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Office Coverage (Next 30 Days)</h2>
                    <div class="flex items-center gap-6 text-gray-800 dark:text-gray-200">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Days with Key Holder</div>
                            <div class="text-2xl font-semibold">{{ number_format($officeDaysWithKey) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Days without Key Holder</div>
                            <div class="text-2xl font-semibold">{{ number_format($officeDaysWithoutKey) }}</div>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">Average daily shifts (next 30 days): <span class="font-medium">{{ number_format($avgDailyShifts, 1) }}</span></div>
                </div>

                <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Shifts per Team (Next 30 Days)</h2>
                    <ul class="space-y-2 text-gray-800 dark:text-gray-200">
                        @forelse($perTeamNext30 as $teamKey => $count)
                            <li class="flex justify-between"><span class="uppercase">{{ $teamKey }}</span><span>{{ number_format($count) }}</span></li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No upcoming shifts</li>
                        @endforelse
                    </ul>
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
    </div>
</x-layouts.app>
