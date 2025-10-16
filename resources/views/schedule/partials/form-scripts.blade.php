@push('scripts')
    <script>
        function deleteSchedule() {
            if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const byId = (id) => document.getElementById(id);
            const [startDateInput, startTimeInput, endDateInput, endTimeInput] = ['start_date', 'start_time', 'end_date', 'end_time'].map(byId);
            const endDateDisplay = byId('end_date_display');
            const warningDiv = byId('duration-warning');
            const warningMessage = byId('duration-message');
            const officeOption = byId('office-option');
            const keyWarning = byId('key-warning');
            const keyWarningMessage = byId('key-warning-message');
            const officeAccessInfo = byId('office-access-info');
            const userHasKeys = {{ $user->hasKeys() ? 'true' : 'false' }};
            const [workType, holidayType, meetingType] = ['work-type', 'holiday-type', 'meeting-type'].map(byId);
            const locationSection = byId('location-section');
            const meetingLocationOption = byId('meeting-location-option');
            const multiDaySection = byId('multi-day-section');
            const consecutiveDaysSelect = byId('consecutive_days');
            const locationInputs = Array.from(document.querySelectorAll('input[name="location"]'));
            const icons = {
                success: '<svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                warning: '<svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
            };
            const toneClasses = {
                success: 'text-green-600 dark:text-green-400',
                warning: 'text-yellow-600 dark:text-yellow-400',
            };
            const officeInfo = (tone, message) => `<div class="${toneClasses[tone]} flex items-center">${icons[tone]}${message}</div>`;
            const toggle = (el, display) => { if (el) el.style.display = display; };
            const setLocation = (value) => locationInputs.forEach((input) => { if (input.value === value) input.checked = true; });
            const locationChecked = (value) => locationInputs.some((input) => input.value === value && input.checked);
            const showWarning = (message) => {
                if (!warningDiv || !warningMessage) return;
                warningDiv.classList.remove('hidden');
                warningDiv.setAttribute('role', 'alert');
                warningMessage.textContent = message;
            };
            const hideWarning = () => {
                if (!warningDiv) return;
                warningDiv.classList.add('hidden');
                warningDiv.removeAttribute('role');
            };

            let currentOfficeAccess = userHasKeys;

            const syncEndDate = () => {
                if (!endDateInput) {
                    return;
                }

                const value = startDateInput?.value || endDateInput.value || '';
                endDateInput.value = value;
                if (endDateDisplay) {
                    endDateDisplay.value = value;
                }
            };

            function updateLocationOptions() {
                if (holidayType?.checked) {
                    toggle(locationSection, 'none');
                    toggle(meetingLocationOption, 'none');
                    toggle(multiDaySection, 'block');
                    setLocation('home');
                } else if (meetingType?.checked) {
                    toggle(locationSection, 'block');
                    toggle(meetingLocationOption, 'block');
                    toggle(multiDaySection, 'none');
                    if (consecutiveDaysSelect) consecutiveDaysSelect.value = '1';
                    setLocation('meeting');
                } else {
                    toggle(locationSection, 'block');
                    toggle(meetingLocationOption, 'none');
                    toggle(multiDaySection, 'block');
                    if (locationChecked('meeting')) {
                        setLocation('home');
                    }
                }
                validateKeys();
            }

            function validateDuration() {
                if (![startDateInput, startTimeInput, endDateInput, endTimeInput].every((input) => input && input.value)) {
                    hideWarning();
                    return;
                }

                const startDateTime = new Date(`${startDateInput.value}T${startTimeInput.value}`);
                const endDateTime = new Date(`${endDateInput.value}T${endTimeInput.value}`);

                if (endDateTime <= startDateTime) {
                    showWarning('End time must be after start time.');
                    return;
                }

                const diffHours = (endDateTime - startDateTime) / (1000 * 60 * 60);

                if (workType?.checked && diffHours < 8) {
                    const actualHours = Math.round(diffHours * 10) / 10;
                    showWarning(`The selected duration is ${actualHours} hours, which is less than the standard 8-hour workday.`);
                    return;
                }

                hideWarning();
            }

            function checkOfficeAccess() {
                if (!startDateInput?.value || !workType?.checked) {
                    return;
                }

                fetch('{{ route("schedule.check-office-access") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ date: startDateInput.value })
                })
                .then((response) => response.json())
                .then((data) => {
                    currentOfficeAccess = data.hasAccess;
                    updateOfficeAccessUI(data);
                })
                .catch((error) => {
                    console.error('Error checking office access:', error);
                });
            }

            function updateOfficeAccessUI(data) {
                if (!officeAccessInfo) {
                    return;
                }

                if (data.hasAccess) {
                    keyWarning?.classList.add('hidden');
                    let message = '';
                    if (Array.isArray(data.keyHolders) && data.keyHolders.length) {
                        message = `Office access available - ${data.keyHolders.join(', ')} will be there with keys`;
                    } else if (userHasKeys) {
                        message = 'You have office keys';
                    }

                    if (message) {
                        officeAccessInfo.innerHTML = officeInfo('success', message);
                        officeAccessInfo.classList.remove('hidden');
                    } else {
                        officeAccessInfo.innerHTML = '';
                        officeAccessInfo.classList.add('hidden');
                    }
                    return;
                }

                officeAccessInfo.innerHTML = officeInfo('warning', 'No one with keys is scheduled for office work on this date');
                officeAccessInfo.classList.remove('hidden');
            }

            function validateKeys() {
                if (!keyWarning) {
                    return true;
                }

                const show = !currentOfficeAccess && !!officeOption?.checked && !!workType?.checked;
                keyWarning.classList.toggle('hidden', !show);

                if (show && keyWarningMessage) {
                    keyWarningMessage.textContent = 'Warning: You cannot work in the office on this date - no one with keys will be there to let you in.';
                }

                return true;
            }

            startDateInput?.addEventListener('change', () => {
                syncEndDate();
                validateDuration();
                checkOfficeAccess();
            });

            [startTimeInput, endDateInput, endTimeInput].forEach((input) => input?.addEventListener('change', validateDuration));
            officeOption?.addEventListener('change', validateKeys);
            [workType, holidayType, meetingType].forEach((input) => input?.addEventListener('change', updateLocationOptions));

            consecutiveDaysSelect?.addEventListener('change', syncEndDate);

            syncEndDate();
            updateLocationOptions();

            document.querySelector('form')?.addEventListener('submit', () => {
                validateKeys();
                validateDuration();
            });
        });
    </script>
@endpush
