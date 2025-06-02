<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/session.php';

class UserController {
    private $user;

    public function __construct() {
        SessionManager::start();
        $this->user = new User();
    }

    public function createUser($data) {
        // Check if user_id already exists
        $existingUser = $this->user->getByUserId($data['user_id']);
        if ($existingUser) {
            return ['success' => false, 'message' => 'User ID already exists'];
        }

        $this->user->user_id = $data['user_id'];
        $this->user->firstname = ucfirst($data['firstname']);
        $this->user->lastname = ucfirst($data['lastname']);
        $this->user->email = $data['email'];
        $this->user->role = $data['role'];
        $this->user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($this->user->create()) {
            return ['success' => true, 'message' => 'User created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create user'];
    }

    public function updateUser($id, $data) {
        $this->user->id = $id;
        $this->user->firstname = ucfirst($data['firstname']);
        $this->user->lastname = ucfirst($data['lastname']);
        $this->user->email = ucfirst($data['email']);
        $this->user->role = ucfirst($data['role']);

        if($this->user->update()) {
            return ['success' => true, 'message' => 'User updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update user'];
    }

    public function deleteUser($id) {
        $this->user->id = $id;
        if($this->user->delete()) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete user'];
    }

    public function resetUserPassword($id) {
        $userData = $this->user->getById($id);
        if(!$userData) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $this->user->id = $id;
        $this->user->user_id = $userData['user_id'];

        if($this->user->resetPassword()) {
            return ['success' => true, 'message' => 'Password reset successfully'];
        }
        return ['success' => false, 'message' => 'Failed to reset password'];
    }

    public function getAllUsers($page = 1) {
        $users = $this->user->getAll($page);
        $total = $this->user->getTotalCount();
        
        return [
            'users' => $users,
            'total' => $total,
            'current_page' => $page,
            'per_page' => 10,
            'total_pages' => ceil($total / 10)
        ];
    }

    public function getUser($id) {
        return $this->user->getById($id);
    }

    public function validateUserData($data, $isCreate = true) {
        $errors = [];

        if($isCreate && empty($data['user_id'])) {
            $errors[] = "User ID is required";
        }
        if($isCreate && empty($data['password'])) {
            $errors[] = "Password is required";
        }
        if(empty($data['firstname'])) {
            $errors[] = "First name is required";
        }
        if(empty($data['lastname'])) {
            $errors[] = "Last name is required";
        }
        if(empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if(empty($data['role']) || !in_array($data['role'], ['admin', 'staff', 'faculty', 'student'])) {
            $errors[] = "Invalid role";
        }

        return $errors;
    }
}
?> 