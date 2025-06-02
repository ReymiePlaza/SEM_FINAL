<?php
require_once 'layout/header.php';

// Get user role from session
$userRole = $_SESSION['user']['role'];

// Define event type permissions
$allowedEventTypes = [
    'student' => ['academic', 'cultural', 'sports', 'other'],
    'faculty' => ['academic', 'meeting', 'cultural', 'sports', 'other'],
    'staff' => ['academic', 'meeting', 'cultural', 'sports', 'other'],
    'admin' => ['academic', 'meeting', 'cultural', 'sports', 'other']
];
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Create New Event</h2>
                </div>
                <div class="card-body">
                    <form id="createEventForm" action="../controllers/EventController.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="venue" class="form-label">Venue *</label>
                            <input type="text" class="form-control" id="venue" name="venue" required>
                        </div>

                        <div class="mb-3">
                            <label for="event_type" class="form-label">Event Type *</label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="">Select Event Type</option>
                                <?php foreach ($allowedEventTypes[$userRole] as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo ucfirst(htmlspecialchars($type)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_datetime" class="form-label">Start Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="start_datetime" 
                                       name="start_datetime" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_datetime" class="form-label">End Date & Time *</label>
                                <input type="datetime-local" class="form-control" id="end_datetime" 
                                       name="end_datetime" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                            <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('createEventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic validation
    const startDate = new Date(document.getElementById('start_datetime').value);
    const endDate = new Date(document.getElementById('end_datetime').value);
    
    if (endDate <= startDate) {
        alert('End date must be after start date');
        return;
    }
    
    // If validation passes, submit the form
    this.submit();
});

// Set minimum date/time for the datetime inputs
const now = new Date();
const nowString = now.toISOString().slice(0, 16);
document.getElementById('start_datetime').min = nowString;
document.getElementById('end_datetime').min = nowString;
</script>

<?php
require_once 'layout/footer.php';
?> 