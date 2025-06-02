<?php
$page_title = 'Events';

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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $success = false;
    
    switch($action) {
        case 'create':
            $validation_errors = $eventController->validateEventData($_POST);
            if (empty($validation_errors)) {
                $result = $eventController->createEvent($_POST);
                $success = $result['success'];
                $message = $result['message'];
            } else {
                $message = implode('<br>', $validation_errors);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? null;
            if ($id) {
                $validation_errors = $eventController->validateEventData($_POST);
                if (empty($validation_errors)) {
                    $result = $eventController->updateEvent($id, $_POST);
                    $success = $result['success'];
                    $message = $result['message'];
                } else {
                    $message = implode('<br>', $validation_errors);
                }
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? null;
            if ($id) {
                $result = $eventController->deleteEvent($id);
                $success = $result['success'];
                $message = $result['message'];
            }
            break;
            
        case 'update_status':
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            if ($id && $status) {
                $result = $eventController->updateEventStatus($id, $status);
                $success = $result['success'];
                $message = $result['message'];
            }
            break;
    }
}

// Get current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$events_data = $eventController->getAllEvents($page);

// Get specific event for editing if requested
$edit_event = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_event = $eventController->getEvent($_GET['id']);
}

ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Events Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="bi bi-plus-circle"></i> Add Event
            </button>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Events Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Venue</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events_data['events'] as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo getEventColor($event['event_type']); ?>">
                                            <?php echo ucfirst($event['event_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($event['start_datetime'])); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($event['end_datetime'])); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-event-id="<?php echo $event['id']; ?>"
                                                style="width: auto;">
                                            <option value="pending" <?php echo $event['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $event['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $event['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="cancelled" <?php echo $event['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td><?php echo htmlspecialchars($event['creator_firstname'] . ' ' . $event['creator_lastname']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewEventModal"
                                                    data-event-id="<?php echo $event['id']; ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEventModal"
                                                    data-event-id="<?php echo $event['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger"
                                                    onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($events_data['total_pages'] > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $events_data['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i === $events_data['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" class="form-control" name="venue" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Type</label>
                        <select class="form-select" name="event_type" required>
                            <option value="">Select Type</option>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="meeting">Meeting</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" class="form-control" name="start_datetime" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" class="form-control" name="end_datetime" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editEventForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_event_id">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" class="form-control" name="venue" id="edit_venue" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Type</label>
                        <select class="form-select" name="event_type" id="edit_event_type" required>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="meeting">Meeting</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" class="form-control" name="start_datetime" id="edit_start_datetime" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" class="form-control" name="end_datetime" id="edit_end_datetime" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Event Modal -->
<div class="modal fade" id="viewEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="view_title"></h4>
                <p id="view_description" class="text-muted"></p>
                <div class="mb-3">
                    <strong>Venue:</strong>
                    <span id="view_venue"></span>
                </div>
                <div class="mb-3">
                    <strong>Type:</strong>
                    <span id="view_event_type"></span>
                </div>
                <div class="mb-3">
                    <strong>Start:</strong>
                    <span id="view_start_datetime"></span>
                </div>
                <div class="mb-3">
                    <strong>End:</strong>
                    <span id="view_end_datetime"></span>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span id="view_status"></span>
                </div>
                <div class="mb-3">
                    <strong>Created By:</strong>
                    <span id="view_creator"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteEventForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete_event_id">
</form>

<script>
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        document.getElementById('delete_event_id').value = eventId;
        document.getElementById('deleteEventForm').submit();
    }
}

// Handle status changes
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const eventId = this.dataset.eventId;
        const status = this.value;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="${eventId}">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    });
});

// Handle edit modal
document.getElementById('editEventModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const eventId = button.dataset.eventId;
    
    // Fetch event details using AJAX
    fetch(`/SEMS/controllers/EventController.php?action=get&id=${eventId}`)
        .then(response => response.json())
        .then(event => {
            document.getElementById('edit_event_id').value = event.id;
            document.getElementById('edit_title').value = event.title;
            document.getElementById('edit_description').value = event.description;
            document.getElementById('edit_venue').value = event.venue;
            document.getElementById('edit_event_type').value = event.event_type;
            document.getElementById('edit_start_datetime').value = event.start_datetime.slice(0, 16);
            document.getElementById('edit_end_datetime').value = event.end_datetime.slice(0, 16);
        });
});

// Handle view modal
document.getElementById('viewEventModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const eventId = button.dataset.eventId;
    
    // Fetch event details using AJAX
    fetch(`/SEMS/controllers/EventController.php?action=get&id=${eventId}`)
        .then(response => response.json())
        .then(event => {
            document.getElementById('view_title').textContent = event.title;
            document.getElementById('view_description').textContent = event.description || 'No description available';
            document.getElementById('view_venue').textContent = event.venue;
            document.getElementById('view_event_type').textContent = event.event_type.charAt(0).toUpperCase() + event.event_type.slice(1);
            document.getElementById('view_start_datetime').textContent = new Date(event.start_datetime).toLocaleString();
            document.getElementById('view_end_datetime').textContent = new Date(event.end_datetime).toLocaleString();
            document.getElementById('view_status').textContent = event.status.charAt(0).toUpperCase() + event.status.slice(1);
            document.getElementById('view_creator').textContent = event.creator_firstname + ' ' + event.creator_lastname;
        });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/admin-layout.php';
?>
