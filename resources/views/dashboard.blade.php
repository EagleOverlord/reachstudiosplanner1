<x-layouts.app :title="__('Dashboard')">
    @include('dashboard.partials.calendar-styles')
    @include('dashboard.partials.calendar-scripts')

    @php
        $filterTypes = ['work' => 'Work', 'holiday' => 'Holiday', 'meeting' => 'Meeting'];
        $filterLocations = ['home' => 'Home', 'office' => 'Office', 'meeting' => 'Meeting'];
        $legendColors = [
            ['color' => '#2563eb', 'label' => 'Home shift'],
            ['color' => '#22c55e', 'label' => 'Office shift'],
            ['color' => '#FF9800', 'label' => 'Holiday'],
            ['color' => '#9C27B0', 'label' => 'Meeting'],
        ];
        $legendIcons = [
            ['icon' => '🔑', 'label' => 'Key holder', 'class' => ''],
            ['icon' => '✏️', 'label' => 'Editable (your shifts)', 'class' => 'text-yellow-500'],
        ];
        $userShifts = collect($shifts)
            ->where('extendedProps.is_own_shift', true)
            ->where('extendedProps.is_upcoming', true)
            ->sortBy('start')
            ->take(5);
    @endphp

    <a href="#calendar" class="skip-link">Filters</a>
    <div class="container mx-auto px-4">
        <div class="mb-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm">
            <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Filters</h3>

            <div class="flex flex-wrap items-end gap-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" id="filter-my" class="rounded border-gray-300 dark:border-gray-600" checked>
                    <span class="text-gray-700 dark:text-gray-300">My shifts only</span>
                </label>

                <div>
                    <label for="filter-team" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Team</label>
                    <select id="filter-team" class="min-w-[10rem] border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm">
                        <option value="">All teams</option>
                        @foreach($teams as $teamKey => $teamName)
                            <option value="{{ $teamKey }}">{{ $teamName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Type</div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($filterTypes as $value => $label)
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" class="filter-type rounded border-gray-300 dark:border-gray-600" value="{{ $value }}" checked>
                                <span class="text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Location</div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($filterLocations as $value => $label)
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" class="filter-location rounded border-gray-300 dark:border-gray-600" value="{{ $value }}" checked>
                                <span class="text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="jump-date" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jump to date</label>
                    <input type="date" id="jump-date" class="border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="flex items-center gap-4 ml-auto">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-weekends" class="rounded border-gray-300 dark:border-gray-600" checked>
                        <span class="text-gray-700 dark:text-gray-300">Show weekends</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-weeknums" class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Week numbers</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="toggle-compact" class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Compact</span>
                    </label>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <button id="filter-reset" type="button" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 underline-offset-2 hover:underline focus:outline-none">
                    Reset filters
                </button>
                <span id="calendar-status" class="sr-only" role="status" aria-live="polite">
                    Showing {{ count($shifts) }} events
                </span>
            </div>
        </div>

        <div id="calendar" aria-label="Team schedule calendar"></div>

        <div class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm">
            <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Key</h3>

            <div class="mb-4">
                <h4 class="font-medium mb-2 text-gray-600 dark:text-gray-300">Shift Locations</h4>
                <ul class="flex flex-wrap gap-x-8 gap-y-3">
                    @foreach($legendColors as $legend)
                        <li class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full" style="background-color:{{ $legend['color'] }}"></span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $legend['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="font-medium mb-2 text-gray-600 dark:text-gray-300">Indicators</h4>
                <ul class="flex flex-wrap gap-x-8 gap-y-3">
                    @foreach($legendIcons as $legend)
                        <li class="flex items-center space-x-2">
                            <span class="{{ $legend['class'] }}">{{ $legend['icon'] }}</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $legend['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 bg-white dark:bg-gray-900 p-4 rounded-lg border border-gray-300 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">My Upcoming Shifts</h2>

            @if($userShifts->isNotEmpty())
                <div class="space-y-2">
                    @foreach($userShifts as $shift)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $shift['backgroundColor'] }}"></div>
                                <div>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">
                                        {{ \Carbon\Carbon::parse($shift['start'])->format('M j, Y') }}
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">
                                        {{ \Carbon\Carbon::parse($shift['start'])->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($shift['end'])->format('g:i A') }}
                                    </span>
                                    @php
                                        $type = $shift['extendedProps']['type'] ?? 'work';
                                        $location = $shift['extendedProps']['location'];
                                    @endphp
                                    <span class="text-gray-600 dark:text-gray-400 text-sm ml-2">
                                        @if($type === 'holiday')
                                            (Holiday)
                                        @elseif($type === 'meeting')
                                            (Meeting{{ $location === 'meeting' ? '' : ' - ' . ucfirst($location) }})
                                        @else
                                            ({{ ucfirst($location) }})
                                        @endif
                                    </span>
                                    @if(($shift['extendedProps']['team_name'] ?? 'No Team') !== 'No Team')
                                        <span class="text-indigo-600 dark:text-indigo-400 text-sm ml-2">
                                            [{{ $shift['extendedProps']['team_name'] }}]
                                        </span>
                                    @endif
                                    @if($shift['extendedProps']['has_key'])
                                        <span class="ml-2" title="Key holder">🔑</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('schedule.edit', $shift['id']) }}"
                                   class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('schedule.destroy', $shift['id']) }}"
                                      class="inline"
                                      data-confirm
                                      data-confirm-title="Delete this shift?"
                                      data-confirm-message="This action cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400">You have no upcoming shifts scheduled.</p>
            @endif
        </div>
    </div>
</x-layouts.app>
