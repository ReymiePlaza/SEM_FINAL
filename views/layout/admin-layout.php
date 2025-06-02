<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

SessionManager::start();
$auth = new AuthController();

if(!$auth->isLoggedIn()) {
    header("Location: /SEMS/views/auth/login.php");
    exit();
}

// Get the current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMS - <?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #212529;
            padding-top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 0.8rem 1rem;
            opacity: 0.8;
        }
        .sidebar .nav-link:hover {
            opacity: 1;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            opacity: 1;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar-header {
            color: white;
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
            background-color: #1a1e21;
        }
        .sidebar-header h4 {
            padding: 0.5rem;
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .user-info {
            color: white;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #1a1e21;
        }
        .nav-section {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .nav-section-title {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.125);
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .badge {
            padding: 0.5em 0.75em;
        }
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>SEMS Admin</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>" 
                   href="/SEMS/views/admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
        </ul>

        <div class="nav-section">
            <div class="nav-section-title">Event Management</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'calendar' ? 'active' : ''; ?>" 
                       href="/SEMS/views/admin/calendar.php">
                        <i class="bi bi-calendar3"></i> Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'event' ? 'active' : ''; ?>" 
                       href="/SEMS/views/admin/event.php">
                        <i class="bi bi-calendar-event"></i> Events
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">User Management</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'users' ? 'active' : ''; ?>" 
                       href="/SEMS/views/admin/users.php">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-info">
            <div class="mb-2">
                <i class="bi bi-person-circle"></i>
                <?php echo (isset($_SESSION['firstname']) ? $_SESSION['firstname'] : 'User') . ' ' . (isset($_SESSION['lastname']) ? $_SESSION['lastname'] : ''); ?>
            </div>
            <a href="/SEMS/views/auth/logout.php" class="btn btn-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
