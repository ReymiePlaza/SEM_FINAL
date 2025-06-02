<?php
require_once '../layout/header.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Staff Dashboard</h1>
    
    <div class="row">
        <!-- Event Creation Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Create Event</h5>
                    <p class="card-text">Create any type of event.</p>
                    <a href="create-event.php" class="btn btn-primary">Create Event</a>
                </div>
            </div>
        </div>

        <!-- Events Management Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Event Management</h5>
                    <p class="card-text">Manage all events.</p>
                    <a href="manage-events.php" class="btn btn-warning">Manage Events</a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">View event statistics and reports.</p>
                    <a href="reports.php" class="btn btn-info">View Reports</a>
                </div>
            </div>
        </div>

        <!-- Calendar View Card -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendar</h5>
                    <p class="card-text">View and manage calendar.</p>
                    <a href="calendar.php" class="btn btn-success">Open Calendar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Overview Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h2>Pending Approvals</h2>
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

        <div class="col-md-6">
            <h2>Today's Events</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Venue</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="todayEvents">
                        <!-- Will be populated by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Events</h5>
                    <h3 id="totalEvents">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h3 id="pendingCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h3 id="approvedCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">This Month</h5>
                    <h3 id="monthlyCount">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../layout/footer.php';
?> 