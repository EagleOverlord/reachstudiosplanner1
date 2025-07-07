<x-layouts.app :title="__('Dashboard')">
    <head>
        <meta charset='utf-8' />
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>

        <style>
            body {
                @apply bg-white dark:bg-gray-900 text-gray-900 dark:text-white;
            }

            #calendar {
                max-width: 1500px;
                margin: 40px auto;
                background-color: white;
                padding: 0;
                border-radius: 6px;
                overflow: hidden;
                box-shadow: 0 0 10px rgba(0,0,0,0.05);
            }

            /* Ensure scroll grid and time labels are visible */
            .fc-col-header, .fc-timegrid-axis, .fc-timegrid-slot-label {
                color: inherit;
                font-weight: 500;
                font-size: 0.85rem;
            }

            /* Dark mode overrides */
            .dark #calendar {
                background-color: #1f2937; /* gray-800 */
                color: white;
            }

            .dark .fc {
                background-color: #1f2937;
            }

            .dark .fc-timegrid-slot-label,
            .dark .fc-col-header-cell-cushion,
            .dark .fc-scrollgrid-sync-inner {
                color: #e5e7eb; /* gray-200 */
            }

            .dark .fc-timegrid-slot {
                border-color: #374151;
            }

            .dark .fc-scrollgrid {
                border-color: #25282c;
            }
        </style>

        <script>
            function initializeCalendar() {
                var calendarEl = document.getElementById('calendar');
                
                // Check if calendar already exists and destroy it
                if (window.dashboardCalendar) {
                    window.dashboardCalendar.destroy();
                }

                if (calendarEl) {
                    window.dashboardCalendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridDay',
                        initialDate: '2025-07-07', // Set to today's date
                        slotMinTime: '07:30:00',
                        slotMaxTime: '19:00:00',
                        allDaySlot: false,
                        slotDuration: '00:30:00',

                        slotLabelFormat: {
                            hour: '2-digit',
                            minute: '2-digit',
                            meridiem: false,
                            hour12: false
                        },

                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },

                        events: @json($shifts),

                        eventDidMount: function(info) {
                            if (info.event.extendedProps.location === 'home') {
                                info.el.style.backgroundColor = '#2563eb'; // blue-600
                                info.el.style.borderColor = '#2563eb';
                            } else if (info.event.extendedProps.location === 'office') {
                                info.el.style.backgroundColor = '#22c55e'; // green-500
                                info.el.style.borderColor = '#22c55e';
                            }
                            
                            // Add hover effect for editable shifts
                            if (info.event.extendedProps.is_editable) {
                                info.el.style.cursor = 'pointer';
                                info.el.title = 'Click to edit your shift';
                            }
                        },
                        
                        eventClick: function(info) {
                            // Allow editing only for user's own upcoming shifts
                            if (info.event.extendedProps.is_editable) {
                                window.location.href = `/schedule/${info.event.id}/edit`;
                            }
                        },

                        eventContent: function(arg) {
                            let title = arg.event.title || '';
                            let hasKey = arg.event.extendedProps.has_key;
                            let keyIcon = hasKey ? ' <span title="Holds key">🔑</span>' : '';
                            let isEditable = arg.event.extendedProps.is_editable;
                            let editIcon = isEditable ? ' <span title="Click to edit" style="color: #fbbf24;">✏️</span>' : '';
                            let name = title.split(' - ')[0];
                            let rest = title.substring(name.length);
                            return { html: `<b>${name}${keyIcon}${editIcon}${rest}</b>` };
                        },

                        eventOverlap: true,
                        eventMaxStack: 20, // allow up to 20 stacked events
                        dayMaxEvents: false, // do not limit number of events per day
                        dayMaxEventRows: false, // do not limit number of event rows
                    });

                    window.dashboardCalendar.render();
                }
            }

            // Initialize on DOM ready
            document.addEventListener('DOMContentLoaded', initializeCalendar);
            
            // Initialize on Livewire navigation
            document.addEventListener('livewire:navigated', initializeCalendar);
        </script>
    </head>

    <body class="dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div id='calendar'></div>
            
            <!-- User's Upcoming Shifts Section -->
            <div class="mt-6 bg-gray-900 p-4 rounded-lg border border-gray-700">
                <h2 class="text-xl font-bold text-gray-100 mb-4">My Upcoming Shifts</h2>
                @php
                    $userShifts = collect($shifts)->where('extendedProps.is_own_shift', true)
                                                 ->where('extendedProps.is_upcoming', true)
                                                 ->sortBy('start')
                                                 ->take(5);
                @endphp
                
                @if($userShifts->count() > 0)
                    <div class="space-y-2">
                        @foreach($userShifts as $shift)
                            <div class="flex items-center justify-between bg-gray-800 p-3 rounded border border-gray-600">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $shift['backgroundColor'] }}"></div>
                                    <div>
                                        <span class="text-gray-200 font-medium">
                                            {{ \Carbon\Carbon::parse($shift['start'])->format('M j, Y') }}
                                        </span>
                                        <span class="text-gray-400 text-sm">
                                            {{ \Carbon\Carbon::parse($shift['start'])->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($shift['end'])->format('g:i A') }}
                                        </span>
                                        <span class="text-gray-400 text-sm ml-2">
                                            ({{ ucfirst($shift['extendedProps']['location']) }})
                                        </span>
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
                    <p class="text-gray-400">You have no upcoming shifts scheduled.</p>
                @endif
            </div>
        </div>
    </body>
</x-layouts.app>