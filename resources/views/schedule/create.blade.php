<x-layouts.app :title="isset($shift) ? __('Edit Schedule') : __('Create Schedule')">
    <div class="max-w-xl mx-auto mt-8">
        <h1 class="text-2xl font-bold text-gray-100 mb-6">
            {{ isset($shift) ? 'Edit Schedule' : 'Create Schedule' }}
        </h1>
        
        <form method="POST" action="{{ isset($shift) ? route('schedule.update', $shift) : route('schedule.store') }}" class="space-y-6 bg-gray-900 p-6 rounded shadow border border-gray-700">
            @csrf
            @if(isset($shift))
                @method('PUT')
            @endif
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Start Date & Time</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" id="start_date" required 
                               value="{{ isset($shift) ? $shift->start_time->format('Y-m-d') : old('start_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}"
                               class="flex-1 border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <select name="start_time" id="start_time" required 
                                class="flex-1 border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @php
                                $currentStartTime = isset($shift) ? $shift->start_time->format('H:i') : old('start_time', '09:00');
                            @endphp
                            @for($hour = 6; $hour <= 23; $hour++)
                                @for($minute = 0; $minute < 60; $minute += 15)
                                    @php
                                        $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                        $timeDisplay = date('g:i A', strtotime($timeValue));
                                    @endphp
                                    <option value="{{ $timeValue }}" {{ $currentStartTime == $timeValue ? 'selected' : '' }}>
                                        {{ $timeDisplay }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">End Date & Time</label>
                    <div class="flex space-x-2">
                        <input type="date" name="end_date" id="end_date" required 
                               value="{{ isset($shift) ? $shift->end_time->format('Y-m-d') : old('end_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}"
                               class="flex-1 border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <select name="end_time" id="end_time" required 
                                class="flex-1 border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @php
                                $currentEndTime = isset($shift) ? $shift->end_time->format('H:i') : old('end_time', '17:00');
                            @endphp
                            @for($hour = 6; $hour <= 23; $hour++)
                                @for($minute = 0; $minute < 60; $minute += 15)
                                    @php
                                        $timeValue = sprintf('%02d:%02d', $hour, $minute);
                                        $timeDisplay = date('g:i A', strtotime($timeValue));
                                    @endphp
                                    <option value="{{ $timeValue }}" {{ $currentEndTime == $timeValue ? 'selected' : '' }}>
                                        {{ $timeDisplay }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-300 mb-2">Work Location</span>
                <div class="flex items-center space-x-4"> 
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="home" 
                               class="form-radio text-indigo-400 bg-gray-800 border-gray-700"
                               {{ (isset($shift) && $shift->location === 'home') || (!isset($shift) && old('location', 'home') === 'home') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-200">Home</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="office" 
                               class="form-radio text-indigo-400 bg-gray-800 border-gray-700" id="office-option"
                               {{ (isset($shift) && $shift->location === 'office') || (!isset($shift) && old('location') === 'office') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-200">Office</span>
                    </label>
                </div>
                <div id="office-access-info" class="mt-2 text-sm hidden">
                    <!-- Dynamic content will be inserted here -->
                </div>
            </div>
            <div id="key-warning" class="hidden bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-4 w-4 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Office Access Warning</p>
                        <p class="text-sm" id="key-warning-message">You don't have office keys and no one with keys is scheduled for office work on this date. You may not be able to access the building.</p>
                    </div>
                </div>
            </div>
            <div id="duration-warning" class="hidden bg-yellow-900 border border-yellow-700 text-yellow-200 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-4 w-4 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-700 text-white font-semibold rounded hover:bg-indigo-800">
                    {{ isset($shift) ? 'Update Schedule' : 'Create Schedule' }}
                </button>
                @if(isset($shift))
                    <button type="button" onclick="deleteSchedule()" class="py-2 px-4 bg-red-700 text-white font-semibold rounded hover:bg-red-800">
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

    <script>
        function deleteSchedule() {
            if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start');
            const endInput = document.getElementById('end');
            const warningDiv = document.getElementById('duration-warning');
            const warningMessage = document.getElementById('duration-message');
            const officeOption = document.getElementById('office-option');
            const keyWarning = document.getElementById('key-warning');
            const keyWarningMessage = document.getElementById('key-warning-message');
            const officeAccessInfo = document.getElementById('office-access-info');
            const userHasKeys = {{ $user->hasKeys() ? 'true' : 'false' }};

            let currentOfficeAccess = userHasKeys;

            function validateDuration() {
                if (startInput.value && endInput.value) {
                    const startTime = new Date(startInput.value);
                    const endTime = new Date(endInput.value);
                    
                    if (endTime <= startTime) {
                        warningDiv.classList.remove('hidden');
                        warningMessage.textContent = 'End time must be after start time.';
                        return;
                    }
                    
                    const diffMs = endTime - startTime;
                    const diffHours = diffMs / (1000 * 60 * 60);
                    
                    if (diffHours < 8) {
                        warningDiv.classList.remove('hidden');
                        const actualHours = Math.round(diffHours * 10) / 10;
                        warningMessage.textContent = `The selected duration is ${actualHours} hours, which is less than the standard 8-hour workday.`;
                    } else {
                        warningDiv.classList.add('hidden');
                    }
                } else {
                    warningDiv.classList.add('hidden');
                }
            }

            function checkOfficeAccess() {
                if (!startInput.value) {
                    return;
                }

                const selectedDate = new Date(startInput.value).toISOString().split('T')[0];
                
                fetch('{{ route("schedule.check-office-access") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        date: selectedDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    currentOfficeAccess = data.hasAccess;
                    updateOfficeAccessUI(data);
                })
                .catch(error => {
                    console.error('Error checking office access:', error);
                });
            }

            function updateOfficeAccessUI(data) {
                if (data.hasAccess) {
                    keyWarning.classList.add('hidden');
                    if (data.keyHolders && data.keyHolders.length > 0) {
                        officeAccessInfo.innerHTML = `
                            <div class="text-green-400 flex items-center">
                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Office access available - ${data.keyHolders.join(', ')} will be there with keys
                            </div>
                        `;
                    } else if (userHasKeys) {
                        officeAccessInfo.innerHTML = `
                            <div class="text-green-400 flex items-center">
                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                You have office keys
                            </div>
                        `;
                    }
                    officeAccessInfo.classList.remove('hidden');
                } else {
                    officeAccessInfo.innerHTML = `
                        <div class="text-yellow-400 flex items-center">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            No one with keys is scheduled for office work on this date
                        </div>
                    `;
                    officeAccessInfo.classList.remove('hidden');
                }
            }

            function validateKeys() {
                if (!currentOfficeAccess && officeOption && officeOption.checked) {
                    keyWarning.classList.remove('hidden');
                    keyWarningMessage.textContent = 'Warning: You cannot work in the office on this date - no one with keys will be there to let you in.';
                    return true; // Allow submission but show warning
                } else {
                    keyWarning.classList.add('hidden');
                    return true;
                }
            }

            startInput.addEventListener('change', function() {
                validateDuration();
                checkOfficeAccess();
            });
            
            endInput.addEventListener('change', validateDuration);
            
            if (officeOption) {
                officeOption.addEventListener('change', validateKeys);
            }

            // Validate on form submission but allow submission with warnings
            document.querySelector('form').addEventListener('submit', function(e) {
                validateKeys();
                validateDuration();
            });
        });
    </script>
</x-layouts.app>