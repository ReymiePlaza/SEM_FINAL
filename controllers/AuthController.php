<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        SessionManager::start();
        $this->user = new User();
    }

    public function login($user_id, $password) {
        $this->user->user_id = $user_id;
        $this->user->password = $password;

        if($this->user->login()) {
            // Regenerate session ID to prevent session fixation
            SessionManager::regenerate();
            
            // Store user data in session
            $_SESSION['user_id'] = $this->user->user_id;
            $_SESSION['firstname'] = $this->user->firstname;
            $_SESSION['lastname'] = $this->user->lastname;
            $_SESSION['email'] = $this->user->email;
            $_SESSION['role'] = $this->user->role;
            $_SESSION['last_activity'] = time();
            $_SESSION['is_logged_in'] = true;

            return true;
        }
        return false;
    }

    public function isLoggedIn() {
        // Debug session data
        error_log('Session data: ' . print_r($_SESSION, true));
        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            error_log('User not logged in');
            return false;
        }

        // Check for session timeout (1 hour)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
            error_log('Session timeout');
            $this->logout();
            return false;
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
        return true;
    }

    public function logout() {
        SessionManager::destroy();
        header("Location: /SEMS/views/auth/login.php");
        exit();
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'firstname' => $_SESSION['firstname'],
                'lastname' => $_SESSION['lastname'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }
}
?> 