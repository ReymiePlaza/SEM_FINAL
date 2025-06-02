<?php
require_once '../layout/header.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Student Dashboard</h1>
    
    <div class="row">
        <!-- Event Creation Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Create Event</h5>
                    <p class="card-text">Request a new event for approval.</p>
                    <a href="create-event.php" class="btn btn-primary">Create Event</a>
                </div>
            </div>
        </div>

        <!-- My Events Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Events</h5>
                    <p class="card-text">View and manage your event requests.</p>
                    <a href="my-events.php" class="btn btn-info">View My Events</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Events</h5>
                    <p class="card-text">Browse all approved upcoming events.</p>
                    <a href="upcoming-events.php" class="btn btn-success">View Events</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events Section -->
    <div class="mt-4">
        <h2>Recent Events</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recentEvents">
                    <!-- Will be populated by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../layout/footer.php';
?> 