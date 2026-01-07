<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthYear,dayGridMonth,timeGridWeek,listWeek'
                },
                views: {
                    multiMonthYear: {
                        type: 'multiMonth',
                        duration: { years: 1 },
                        buttonText: 'Year'
                    }
                },
                events: {!! $events !!},
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                height: 'auto',
                themeSystem: 'standard'
            });
            calendar.render();
        });
    </script>
    @endpush

    <style>
        #calendar {
            max-width: 100%;
        }
        .fc-event {
            cursor: pointer;
        }
        .fc-toolbar-title {
            font-size: 1.25rem !important;
        }
        .dark .fc {
            color: #e5e7eb;
        }
        .dark .fc-day-today {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        .dark .fc-scrollgrid {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .dark .fc-theme-standard td, 
        .dark .fc-theme-standard th {
            border-color: rgba(255, 255, 255, 0.1);
        }
        /* Year view styling */
        .fc-multimonth {
            overflow-x: auto;
        }
        .fc-multimonth-month {
            margin: 0.5rem;
        }
        .fc-multimonth-title {
            font-size: 1rem !important;
            font-weight: 600;
        }
    </style>
</x-filament-panels::page>
