<x-layouts.app :title="__('Dashboard')">
    @push('styles')
        
        <style>
            /* Base calendar styling */
            #calendar { max-width: 1500px; margin: 40px auto; background-color: white; padding: 0; border-radius: 6px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
            .fc-col-header, .fc-timegrid-axis, .fc-timegrid-slot-label { color: inherit; font-weight: 500; font-size: 0.85rem; }
            
            /* Light mode FullCalendar button styling */
            .fc-button {
                background-color: #f3f4f6 !important;
                border-color: #d1d5db !important;
                color: #374151 !important;
                font-weight: 500 !important;
                padding: 0.5rem 0.75rem !important;
                border-radius: 0.375rem !important;
                transition: all 0.2s ease !important;
            }
            
            .fc-button:hover {
                background-color: #e5e7eb !important;
                border-color: #9ca3af !important;
                color: #1f2937 !important;
            }
            
            .fc-button-active, .fc-button:focus {
                background-color: #ef4444 !important;
                border-color: #ef4444 !important;
                color: white !important;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5) !important;
            }
            
            .fc-button:disabled {
                background-color: #f9fafb !important;
                border-color: #e5e7eb !important;
                color: #9ca3af !important;
                opacity: 0.6 !important;
                cursor: not-allowed !important;
            }
            
            /* Light mode toolbar styling */
            .fc-toolbar {
                background-color: transparent !important;
                padding: 1rem !important;
                border-bottom: 1px solid #e5e7eb !important;
            }
            
            .fc-toolbar-title {
                color: #1f2937 !important;
                font-weight: 600 !important;
                font-size: 1.25rem !important;
            }
            
            /* Dark mode base styling */
            .dark #calendar, .dark .fc { 
                background-color: #1f2937; 
                color: white; 
            }
            
            .dark .fc-timegrid-slot-label, 
            .dark .fc-col-header-cell-cushion, 
            .dark .fc-scrollgrid-sync-inner { 
                color: #e5e7eb; 
            }
            
            .dark .fc-timegrid-slot { 
                border-color: #374151; 
            }
            
            .dark .fc-scrollgrid { 
                border-color: #25282c; 
            }
            
            /* Dark mode FullCalendar button styling */
            .dark .fc-button {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
                color: #e5e7eb !important;
                font-weight: 500 !important;
                padding: 0.5rem 0.75rem !important;
                border-radius: 0.375rem !important;
                transition: all 0.2s ease !important;
            }
            
            .dark .fc-button:hover {
                background-color: #4b5563 !important;
                border-color: #6b7280 !important;
                color: #f9fafb !important;
            }
            
            .dark .fc-button-active, 
            .dark .fc-button:focus {
                background-color: #ef4444 !important;
                border-color: #ef4444 !important;
                color: white !important;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5) !important;
            }
            
            .dark .fc-button:disabled {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
                color: #6b7280 !important;
                opacity: 0.6 !important;
                cursor: not-allowed !important;
            }
            
            /* Dark mode toolbar styling */
            .dark .fc-toolbar {
                background-color: transparent !important;
                padding: 1rem !important;
                border-bottom: 1px solid #374151 !important;
            }
            
            .dark .fc-toolbar-title {
                color: #f9fafb !important;
                font-weight: 600 !important;
                font-size: 1.25rem !important;
            }
            
            /* Additional styling for better button grouping */
            .fc-button-group .fc-button {
                margin-right: 0 !important;
            }
            
            .fc-button-group .fc-button:first-child {
                border-top-right-radius: 0 !important;
                border-bottom-right-radius: 0 !important;
            }
            
            .fc-button-group .fc-button:last-child {
                border-top-left-radius: 0 !important;
                border-bottom-left-radius: 0 !important;
            }
            
            .fc-button-group .fc-button:not(:first-child):not(:last-child) {
                border-radius: 0 !important;
            }
            
            /* Ensure proper spacing between button groups */
            .fc-toolbar-chunk {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
        </style>
    @endpush

    @push('scripts')
        
        <script>
            (function(){
                const MAX_ATTEMPTS = 20; // ~2s with 100ms interval
                let attempts = 0;

                const buildCalendar = () => {
                    const calendarEl = document.getElementById('calendar');
                    if (!calendarEl || !window.FullCalendar) return false;
                    if (window.dashboardCalendar) {
                        try { window.dashboardCalendar.destroy(); } catch(e) {}
                    }
                    const today = new Date().toISOString().slice(0,10);
                    try {
                        window.dashboardCalendar = new FullCalendar.Calendar(calendarEl, {
                            plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.listPlugin, FullCalendar.interactionPlugin],
                            initialView: 'timeGridWeek',
                            initialDate: today,
                            slotMinTime: '07:30:00',
                            slotMaxTime: '19:00:00',
                            allDaySlot: false,
                            slotDuration: '00:30:00',
                            slotLabelFormat: { hour: '2-digit', minute: '2-digit', meridiem: false, hour12: false },
                            headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,timeGridDay' },
                            events: @json($shifts),
                            eventDidMount: info => {
                                if (info.event.extendedProps.is_editable) {
                                    info.el.style.cursor = 'pointer';
                                    info.el.title = 'Click to edit your shift';
                                }
                            },
                            eventClick: info => { if (info.event.extendedProps.is_editable) { window.location.href = `/schedule/${info.event.id}/edit`; } },
                            eventContent: arg => {
                                const props = arg.event.extendedProps;
                                const name = props.name || '';
                                const location = props.location_display || '';
                                const team = props.team_name_display || '';
                                const hasKey = props.has_key;
                                const isEditable = props.is_editable;

                                const container = document.createElement('div');
                                container.style.fontWeight = 'bold';

                                const nameSpan = document.createElement('span');
                                nameSpan.textContent = name;
                                container.appendChild(nameSpan);

                                if (hasKey) {
                                    const keyIcon = document.createElement('span');
                                    keyIcon.title = 'Holds key';
                                    keyIcon.textContent = ' 🔑';
                                    container.appendChild(keyIcon);
                                }

                                if (isEditable) {
                                    const editIcon = document.createElement('span');
                                    editIcon.title = 'Click to edit';
                                    editIcon.style.color = '#fbbf24';
                                    editIcon.textContent = ' ✏️';
                                    container.appendChild(editIcon);
                                }

                                const detailsSpan = document.createElement('span');
                                detailsSpan.textContent = ` - ${location} (${team})`;
                                container.appendChild(detailsSpan);

                                return { domNodes: [container] };
                            },
                            eventOverlap: false,
                            eventMaxStack: 20,
                            dayMaxEvents: false,
                            dayMaxEventRows: false,
                        });
                        window.dashboardCalendar.render();
                        return true;
                    } catch (e) {
                        console.error('Calendar init error:', e);
                        return false;
                    }
                };

                const attemptInit = () => {
                    if (buildCalendar()) return;
                    attempts++;
                    if (attempts < MAX_ATTEMPTS) setTimeout(attemptInit, 100);
                };

                // Immediate attempt (script placed after DOM for this view)
                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    attemptInit();
                } else {
                    document.addEventListener('DOMContentLoaded', attemptInit, { once: true });
                }

                // Re-init after Livewire navigation
                document.addEventListener('livewire:navigated', () => setTimeout(attemptInit, 0));

                // Optional: expose manual refresh
                window.refreshDashboardCalendar = attemptInit;
            })();
        </script>
    @endpush

    <div class="container mx-auto px-4">
        <div id='calendar'></div>
        <!-- Calendar Legend / Key -->
        <div class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-sm">
            <h3 class="font-semibold mb-3 text-gray-700 dark:text-gray-200">Key</h3>
            
            <!-- Location Legend -->
            <div class="mb-4">
                <h4 class="font-medium mb-2 text-gray-600 dark:text-gray-300">Shift Locations</h4>
                <ul class="flex flex-wrap gap-x-8 gap-y-3">
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#2563eb"></span>
                        <span class="text-gray-600 dark:text-gray-300">Home shift</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#22c55e"></span>
                        <span class="text-gray-600 dark:text-gray-300">Office shift</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#FF9800"></span>
                        <span class="text-gray-600 dark:text-gray-300">Holiday</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full" style="background-color:#9C27B0"></span>
                        <span class="text-gray-600 dark:text-gray-300">Meeting</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span>🔑</span>
                        <span class="text-gray-600 dark:text-gray-300">Key holder</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <span class="text-yellow-500">✏️</span>
                        <span class="text-gray-600 dark:text-gray-300">Editable (your shifts)</span>
                    </li>
                </ul>
            </div>

            
        </div>
            
            <!-- User's Upcoming Shifts Section -->
            <div class="mt-6 bg-white dark:bg-gray-900 p-4 rounded-lg border border-gray-300 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">My Upcoming Shifts</h2>
                @php
                    $userShifts = collect($shifts)->where('extendedProps.is_own_shift', true)
                                                 ->where('extendedProps.is_upcoming', true)
                                                 ->sortBy('start')
                                                 ->take(5);
                @endphp
                
                @if($userShifts->count() > 0)
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
                                        @if($shift['extendedProps']['team_name'] !== 'No Team')
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
                                          onsubmit="return confirm('Are you sure you want to delete this shift?')" 
                                          class="inline">
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