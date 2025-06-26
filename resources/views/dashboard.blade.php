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
                box-shadow: 0 0 10px rgba(0,0,0,0.05);cssc

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
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridDay',
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
                    },

                    eventContent: function(arg) {
                        let title = arg.event.title || '';
                        let hasKey = arg.event.extendedProps.has_key;
                        let keyIcon = hasKey ? ' <span title="Holds key">🔑</span>' : '';
                        let name = title.split(' - ')[0];
                        let rest = title.substring(name.length);
                        return { html: `<b>${name}${keyIcon}${rest}</b>` };
                    },

                    eventOverlap: true,
                    eventMaxStack: 20, // allow up to 20 stacked events
                    dayMaxEvents: false, // do not limit number of events per day
                    dayMaxEventRows: false, // do not limit number of event rows
                });

                calendar.render();
            });
        </script>
    </head>

    <body class="dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div id='calendar'></div>
        </div>
    </body>
</x-layouts.app>