<?php
$page_title = 'Calendar';

require_once __DIR__ . '/../../controllers/EventController.php';
$eventController = new EventController();

// Helper function for event colors
function getEventColor($eventType) {
    switch($eventType) {
        case 'academic':
            return '#0d6efd'; // Bootstrap primary
        case 'sports':
            return '#198754'; // Bootstrap success
        case 'cultural':
            return '#dc3545'; // Bootstrap danger
        case 'meeting':
            return '#ffc107'; // Bootstrap warning
        default:
            return '#6c757d'; // Bootstrap secondary
    }
}

ob_start();
?>

<!-- Add FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.8/main.min.css' rel='stylesheet' />

<style>
.fc-event {
    cursor: pointer;
}
.event-legend {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}
.fc-toolbar-title {
    text-transform: capitalize;
}
.fc .fc-toolbar.fc-header-toolbar {
    margin-bottom: 1.5rem;
}
.fc .fc-button-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.fc .fc-button-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
.fc .fc-button-primary:disabled {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #0a58ca;
    border-color: #0a53be;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Event Calendar</h2>
            <a href="/SEMS/views/admin/event.php" class="btn btn-primary">
                <i class="bi bi-list-ul"></i> Manage Events
            </a>
        </div>

        <!-- Event Type Legend -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Event Types</h5>
                <div class="event-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #0d6efd;"></div>
                        <span>Academic</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #198754;"></div>
                        <span>Sports</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Cultural</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span>Meeting</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6c757d;"></div>
                        <span>Other</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="eventTitle"></h4>
                <p id="eventDescription" class="text-muted"></p>
                <div class="mb-3">
                    <strong>Venue:</strong>
                    <span id="eventVenue"></span>
                </div>
                <div class="mb-3">
                    <strong>Type:</strong>
                    <span id="eventType"></span>
                </div>
                <div class="mb-3">
                    <strong>Start:</strong>
                    <span id="eventStart"></span>
                </div>
                <div class="mb-3">
                    <strong>End:</strong>
                    <span id="eventEnd"></span>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span id="eventStatus"></span>
                </div>
                <div class="mb-3">
                    <strong>Created By:</strong>
                    <span id="eventCreator"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="editEventBtn" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Event
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
// Event color helper functions
function getEventColor(eventType) {
    switch(eventType) {
        case 'academic':
            return '#0d6efd'; // Bootstrap primary
        case 'sports':
            return '#198754'; // Bootstrap success
        case 'cultural':
            return '#dc3545'; // Bootstrap danger
        case 'meeting':
            return '#ffc107'; // Bootstrap warning
        default:
            return '#6c757d'; // Bootstrap secondary
    }
}

function getEventTextColor(eventType) {
    // Use white text for dark backgrounds, black for light backgrounds
    switch(eventType) {
        case 'meeting':
            return '#000000'; // Black text for yellow background
        default:
            return '#ffffff'; // White text for other backgrounds
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    // Debug current date
    console.log('Current date:', new Date().toISOString());
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: '2025-05-24',
        timeZone: 'local', // Explicitly set timezone to local
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        views: {
            dayGridMonth: {
                titleFormat: { month: 'long', year: 'numeric' }
            },
            timeGridWeek: {
                titleFormat: { month: 'long', year: 'numeric', day: 'numeric' }
            },
            timeGridDay: {
                titleFormat: { month: 'long', year: 'numeric', day: 'numeric' }
            },
            listMonth: {
                titleFormat: { month: 'long', year: 'numeric' }
            }
        },
        events: function(info, successCallback, failureCallback) {
            const startStr = info.startStr;
            const endStr = info.endStr;
            console.log('Calendar requesting events from', startStr, 'to', endStr);
            
            // Fetch events for the current view's date range
            fetch(`/SEMS/controllers/EventController.php?action=getByDateRange&start=${startStr}&end=${endStr}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(events => {
                    console.log('Raw events from server:', events);
                    
                    if (!Array.isArray(events)) {
                        console.error('Server did not return an array:', events);
                        failureCallback(new Error('Invalid server response'));
                        return;
                    }
                    
                    const formattedEvents = events.map(event => {
                        // Convert server datetime to local datetime
                        const startDate = new Date(event.start_datetime);
                        const endDate = new Date(event.end_datetime);
                        
                        console.log('Processing event:', {
                            id: event.id,
                            title: event.title,
                            originalStart: event.start_datetime,
                            originalEnd: event.end_datetime,
                            parsedStart: startDate.toISOString(),
                            parsedEnd: endDate.toISOString()
                        });
                        
                        return {
                            id: event.id,
                            title: event.title,
                            start: startDate.toISOString(),
                            end: endDate.toISOString(),
                            description: event.description,
                            venue: event.venue,
                            eventType: event.event_type,
                            status: event.status,
                            creator: event.creator_firstname + ' ' + event.creator_lastname,
                            backgroundColor: getEventColor(event.event_type),
                            borderColor: getEventColor(event.event_type),
                            textColor: getEventTextColor(event.event_type),
                            allDay: false
                        };
                    });
                    
                    console.log('Formatted events for calendar:', formattedEvents);
                    successCallback(formattedEvents);
                })
                .catch(error => {
                    console.error('Error fetching or processing events:', error);
                    failureCallback(error);
                });
        },
        eventDidMount: function(info) {
            console.log('Event mounted:', {
                id: info.event.id,
                title: info.event.title,
                start: info.event.start?.toISOString(),
                end: info.event.end?.toISOString()
            });
            
            // Add tooltips to events
            new bootstrap.Tooltip(info.el, {
                title: `${info.event.title}\nVenue: ${info.event.extendedProps.venue}\nType: ${info.event.extendedProps.eventType}`,
                placement: 'top',
                trigger: 'hover',
                html: true
            });
        },
        loading: function(isLoading) {
            console.log('Calendar loading state:', isLoading);
        },
        dayMaxEvents: true,
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        }
    });
    
    // Render the calendar
    calendar.render();
    
    // Force calendar to go to May 2025 and log the current date
    console.log('Setting calendar to May 2025');
    calendar.gotoDate('2025-05-24');
    console.log('Current calendar date:', calendar.getDate().toISOString());
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/admin-layout.php';
?> 