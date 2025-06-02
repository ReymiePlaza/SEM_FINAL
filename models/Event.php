<?php
require_once __DIR__ . '/../config/db.php';

class Event {
    private $conn;
    private $table = "events";

    public $id;
    public $title;
    public $description;
    public $venue;
    public $event_type;
    public $start_datetime;
    public $end_datetime;
    public $status;
    public $created_by;
    public $approved_by;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                (title, description, venue, event_type, start_datetime, end_datetime, status, created_by)
                VALUES
                (:title, :description, :venue, :event_type, :start_datetime, :end_datetime, :status, :created_by)";
    
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->venue = htmlspecialchars(strip_tags($this->venue));
        
        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':event_type', $this->event_type);
        $stmt->bindParam(':start_datetime', $this->start_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created_by', $this->created_by);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . "
                SET title = :title,
                    description = :description,
                    venue = :venue,
                    event_type = :event_type,
                    start_datetime = :start_datetime,
                    end_datetime = :end_datetime,
                    status = :status,
                    approved_by = :approved_by
                WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->venue = htmlspecialchars(strip_tags($this->venue));
        
        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':venue', $this->venue);
        $stmt->bindParam(':event_type', $this->event_type);
        $stmt->bindParam(':start_datetime', $this->start_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':approved_by', $this->approved_by);
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

    public function getById($id) {
        $query = "SELECT e.*, 
                        u1.firstname as creator_firstname, u1.lastname as creator_lastname,
                        u2.firstname as approver_firstname, u2.lastname as approver_lastname
                 FROM " . $this->table . " e
                 LEFT JOIN users u1 ON e.created_by = u1.id
                 LEFT JOIN users u2 ON e.approved_by = u2.id
                 WHERE e.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($page = 1, $per_page = 10, $filters = []) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT e.*, 
                        u1.firstname as creator_firstname, u1.lastname as creator_lastname,
                        u2.firstname as approver_firstname, u2.lastname as approver_lastname
                 FROM " . $this->table . " e
                 LEFT JOIN users u1 ON e.created_by = u1.id
                 LEFT JOIN users u2 ON e.approved_by = u2.id
                 WHERE 1=1";
        
        // Add filters
        if (!empty($filters['status'])) {
            $query .= " AND e.status = :status";
        }
        if (!empty($filters['event_type'])) {
            $query .= " AND e.event_type = :event_type";
        }
        if (!empty($filters['created_by'])) {
            $query .= " AND e.created_by = :created_by";
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(e.start_datetime) >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(e.end_datetime) <= :end_date";
        }
        
        $query .= " ORDER BY e.start_datetime DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind filters
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        if (!empty($filters['event_type'])) {
            $stmt->bindParam(':event_type', $filters['event_type']);
        }
        if (!empty($filters['created_by'])) {
            $stmt->bindParam(':created_by', $filters['created_by']);
        }
        if (!empty($filters['start_date'])) {
            $stmt->bindParam(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $stmt->bindParam(':end_date', $filters['end_date']);
        }
        
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventsByDateRange($start_date, $end_date) {
        $query = "SELECT e.*, 
                        u1.firstname as creator_firstname, u1.lastname as creator_lastname,
                        u2.firstname as approver_firstname, u2.lastname as approver_lastname
                 FROM " . $this->table . " e
                 LEFT JOIN users u1 ON e.created_by = u1.id
                 LEFT JOIN users u2 ON e.approved_by = u2.id
                 WHERE (start_datetime BETWEEN :start_date AND :end_date)
                 OR (end_datetime BETWEEN :start_date AND :end_date)
                 OR (start_datetime <= :start_date AND end_datetime >= :end_date)
                 ORDER BY start_datetime ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $approved_by = null) {
        $query = "UPDATE " . $this->table . "
                SET status = :status,
                    approved_by = :approved_by
                WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':approved_by', $approved_by);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getTotalCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        // Add filters
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
        }
        if (!empty($filters['event_type'])) {
            $query .= " AND event_type = :event_type";
        }
        if (!empty($filters['created_by'])) {
            $query .= " AND created_by = :created_by";
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(start_datetime) >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(end_datetime) <= :end_date";
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Bind filters
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        if (!empty($filters['event_type'])) {
            $stmt->bindParam(':event_type', $filters['event_type']);
        }
        if (!empty($filters['created_by'])) {
            $stmt->bindParam(':created_by', $filters['created_by']);
        }
        if (!empty($filters['start_date'])) {
            $stmt->bindParam(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $stmt->bindParam(':end_date', $filters['end_date']);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getUserIdFromSession($user_id) {
        $query = "SELECT id FROM users WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        }
        return null;
    }

    public function getCalendarEvents($start, $end) {
        $query = "SELECT * FROM " . $this->table . "
                 WHERE (start_datetime BETWEEN :start AND :end)
                 OR (end_datetime BETWEEN :start AND :end)
                 OR (start_datetime <= :start AND end_datetime >= :end)
                 ORDER BY start_datetime ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 