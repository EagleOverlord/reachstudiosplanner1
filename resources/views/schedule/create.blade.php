<x-layouts.app :title="isset($shift) ? __('Edit Schedule') : __('Create Schedule')">
    <div class="max-w-xl mx-auto mt-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            {{ isset($shift) ? 'Edit Schedule' : 'Create Schedule' }}
        </h1>
        
        <form method="POST" action="{{ isset($shift) ? route('schedule.update', $shift) : route('schedule.store') }}" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded shadow border border-gray-200 dark:border-gray-700">
            @csrf
            @if(isset($shift))
                @method('PUT')
            @endif
            @php
                $resolvedDefaultDate = $defaultDate ?? \Carbon\Carbon::tomorrow()->format('Y-m-d');
                $fieldConfig = [
                    'start' => [
                        'label' => 'Start Date & Time',
                        'date' => isset($shift) ? $shift->start_time->format('Y-m-d') : old('start_date', $prefill['start_date'] ?? $resolvedDefaultDate),
                        'time' => isset($shift) ? $shift->start_time->format('H:i') : old('start_time', $prefill['start_time'] ?? '09:00'),
                    ],
                    'end' => [
                        'label' => 'End Date & Time',
                        'date' => isset($shift) ? $shift->end_time->format('Y-m-d') : old('end_date', $prefill['end_date'] ?? $resolvedDefaultDate),
                        'time' => isset($shift) ? $shift->end_time->format('H:i') : old('end_time', $prefill['end_time'] ?? '17:00'),
                    ],
                ];
                $timeOptions = [];
                for ($hour = 6; $hour <= 23; $hour++) {
                    for ($minute = 0; $minute < 60; $minute += 15) {
                        $value = sprintf('%02d:%02d', $hour, $minute);
                        $timeOptions[$value] = date('g:i A', strtotime($value));
                    }
                }
                $selectedType = isset($shift) ? ($shift->type ?? 'work') : old('type', 'work');
                $scheduleTypes = [
                    ['value' => 'work', 'label' => 'Work', 'id' => 'work-type'],
                    ['value' => 'holiday', 'label' => 'Holiday', 'id' => 'holiday-type'],
                    ['value' => 'meeting', 'label' => 'Meeting', 'id' => 'meeting-type'],
                ];
                $selectedLocation = isset($shift) ? ($shift->location ?? 'home') : old('location', 'home');
                $locationOptions = [
                    ['value' => 'home', 'label' => 'Home'],
                    ['value' => 'office', 'label' => 'Office', 'input_id' => 'office-option'],
                    ['value' => 'meeting', 'label' => 'Meeting Location', 'label_id' => 'meeting-location-option', 'label_attributes' => 'style="display: none;"'],
                ];
                $consecutiveDays = old('consecutive_days', 1);
            @endphp

            <div class="flex space-x-4">
                @foreach($fieldConfig as $prefix => $config)
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $config['label'] }}</label>
                        <div class="flex space-x-2">
                            @if($prefix === 'end')
                                <input type="hidden" name="end_date" id="end_date" value="{{ $config['date'] }}">
                                <input
                                    type="date"
                                    id="end_date_display"
                                    value="{{ $config['date'] }}"
                                    disabled
                                    class="flex-1 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-md shadow-sm cursor-not-allowed"
                                    aria-describedby="end-date-help"
                                >
                            @else
                                <input
                                    type="date"
                                    name="{{ $prefix }}_date"
                                    id="{{ $prefix }}_date"
                                    required
                                    value="{{ $config['date'] }}"
                                    class="flex-1 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            @endif
                            <select
                                name="{{ $prefix }}_time"
                                id="{{ $prefix }}_time"
                                required
                                class="flex-1 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $config['time'] === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($prefix === 'end')
                            <p id="end-date-help" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                End date always matches the start date.
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Multi-day booking section -->
            @if(!isset($shift))
            <div id="multi-day-section">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Consecutive Days</label>
                <select
                    name="consecutive_days"
                    id="consecutive_days"
                    class="w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    @foreach(range(1, 5) as $days)
                        @php
                            $daysLabel = $days === 1 ? '1 Day (Default)' : $days . ' Days';
                        @endphp
                        <option value="{{ $days }}" {{ (int) $consecutiveDays === $days ? 'selected' : '' }}>
                            {{ $daysLabel }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Will create shifts for consecutive weekdays starting from the selected start date. Weekends will be automatically skipped.
                </p>
            </div>
            @endif

            <div>
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Schedule Type</span>
                <div class="flex items-center space-x-4">
                    @foreach($scheduleTypes as $type)
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="type"
                                value="{{ $type['value'] }}"
                                id="{{ $type['id'] }}"
                                class="form-radio text-indigo-400 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                                {{ $selectedType === $type['value'] ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $type['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div id="location-section">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Location</span>
                <div class="flex items-center space-x-4">
                    @foreach($locationOptions as $option)
                        <label class="inline-flex items-center"
                            @if(!empty($option['label_id'])) id="{{ $option['label_id'] }}" @endif
                            @if(!empty($option['label_attributes'])) {!! $option['label_attributes'] !!} @endif
                        >
                            <input
                                type="radio"
                                name="location"
                                value="{{ $option['value'] }}"
                                @if(!empty($option['input_id'])) id="{{ $option['input_id'] }}" @endif
                                class="form-radio text-indigo-400 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
                                {{ $selectedLocation === $option['value'] ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $option['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                <div id="office-access-info" class="mt-2 text-sm hidden">
                    <!-- Dynamic content will be inserted here -->
                </div>
            </div>
            <div id="key-warning" role="alert" class="hidden bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-4 w-4 text-red-600 dark:text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Office Access Warning</p>
                        <p class="text-sm" id="key-warning-message">You don't have office keys and no one with keys is scheduled for office work on this date. You may not be able to access the building.</p>
                    </div>
                </div>
            </div>
            <div id="duration-warning" class="hidden bg-yellow-100 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-4 w-4 text-yellow-600 dark:text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Duration Warning</p>
                        <p class="text-sm" id="duration-message">The selected time range is less than 8 hours. Consider scheduling a full 8-hour workday.</p>
                    </div>
                </div>
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 dark:bg-indigo-700 text-white font-semibold rounded hover:bg-indigo-700 dark:hover:bg-indigo-800">
                    {{ isset($shift) ? 'Update Schedule' : 'Create Schedule' }}
                </button>
                @if(isset($shift))
                    <button type="button" onclick="deleteSchedule()" class="py-2 px-4 bg-red-600 dark:bg-red-700 text-white font-semibold rounded hover:bg-red-700 dark:hover:bg-red-800">
                        Delete
                    </button>
                @endif
            </div>
        </form>
        
        @if(isset($shift))
            <form id="delete-form" method="POST" action="{{ route('schedule.destroy', $shift) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    @include('schedule.partials.form-scripts')
</x-layouts.app>
