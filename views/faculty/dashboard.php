<?php
require_once '../layout/header.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Faculty Dashboard</h1>
    
    <div class="row">
        <!-- Event Creation Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Create Event</h5>
                    <p class="card-text">Create a new academic or other event.</p>
                    <a href="create-event.php" class="btn btn-primary">Create Event</a>
                </div>
            </div>
        </div>

        <!-- Events to Approve Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Approvals</h5>
                    <p class="card-text">Review and approve student event requests.</p>
                    <a href="pending-approvals.php" class="btn btn-warning">View Pending</a>
                </div>
            </div>
        </div>

        <!-- My Events Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Events</h5>
                    <p class="card-text">Manage your created events.</p>
                    <a href="my-events.php" class="btn btn-info">View My Events</a>
                </div>
            </div>
        </div>

        <!-- Calendar View Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendar</h5>
                    <p class="card-text">View events calendar.</p>
                    <a href="calendar.php" class="btn btn-success">Open Calendar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals Section -->
    <div class="mt-4">
        <h2>Recent Pending Approvals</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pendingApprovals">
                    <!-- Will be populated by AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Events Section -->
    <div class="mt-4">
        <h2>Upcoming Events</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="upcomingEvents">
                    <!-- Will be populated by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../layout/footer.php';
?> 