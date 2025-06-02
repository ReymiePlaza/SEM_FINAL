<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $conn;
    private $table = "users";

    public $id;
    public $user_id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            
            // Debug login query
            error_log('Login query for user_id: ' . $this->user_id);
            
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Debug password verification
                error_log('Attempting password verification for user: ' . $this->user_id);
                
                if(password_verify($this->password, $row['password'])) {
                    // Set all user properties
                    $this->id = $row['id'];
                    $this->firstname = $row['firstname'];
                    $this->lastname = $row['lastname'];
                    $this->email = $row['email'];
                    $this->role = $row['role'];
                    
                    // Debug successful login
                    error_log('Login successful for user: ' . $this->user_id);
                    error_log('User data: ' . print_r($row, true));
                    
                    return true;
                }
                error_log('Password verification failed for user: ' . $this->user_id);
            }
            error_log('No user found with user_id: ' . $this->user_id);
            return false;
        } catch(PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return false;
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                (user_id, firstname, lastname, email, password, role)
                VALUES
                (:user_id, :firstname, :lastname, :email, :password, :role)";
    
        $stmt = $this->conn->prepare($query);
        
        // Hash the provided password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Clean data
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $this->role);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . "
                SET firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    role = :role
                WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind data
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function resetPassword() {
        $query = "UPDATE " . $this->table . "
                SET password = :password
                WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        
        // Reset password to user_id
        $hashed_password = password_hash($this->user_id, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT * FROM " . $this->table . "
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 