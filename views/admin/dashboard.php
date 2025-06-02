<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';

$auth = new AuthController();

if(!$auth->isLoggedIn()) {
    header("Location: /SEMS/views/auth/login.php");
    exit();
}

$page_title = 'Dashboard';

ob_start();
?>

<div class="row">
    <div class="col-12">
        <h2>Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-calendar-event"></i> Total Events
                        </h5>
                        <h2 class="mt-3">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-people"></i> Total Users
                        </h5>
                        <h2 class="mt-3">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-calendar-check"></i> Upcoming Events
                        </h5>
                        <h2 class="mt-3">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Events</h5>
                        <p class="card-text">No events found.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/admin-layout.php';
?> 