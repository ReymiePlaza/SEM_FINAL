<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../config/session.php';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $controller = new EventController();

    switch ($_GET['action']) {
        case 'get':
            $id = $_GET['id'] ?? null;
            if ($id) {
                echo json_encode($controller->getEvent($id));
            }
            break;
            
        case 'getByDateRange':
            $start = $_GET['start'] ?? null;
            $end = $_GET['end'] ?? null;
            if ($start && $end) {
                error_log("Fetching events from $start to $end");
                $events = $controller->getEventsByDateRange($start, $end);
                error_log("Found " . count($events) . " events");
                error_log("Events data: " . json_encode($events));
                echo json_encode($events);
            } else {
                error_log("Missing start or end date parameters");
                echo json_encode(['error' => 'Missing date parameters']);
            }
            break;
    }
    exit;
}

class EventController {
    private $event;

    public function __construct() {
        SessionManager::start();
        $this->event = new Event();
    }

    public function createEvent($data) {
        // Get the numeric ID from the users table based on the session user_id
        $user_id = $this->event->getUserIdFromSession($_SESSION['user_id']);
        
        if (!$user_id) {
            return ['success' => false, 'message' => 'Invalid user session'];
        }

        $this->event->title = $data['title'];
        $this->event->description = $data['description'];
        $this->event->venue = $data['venue'];
        $this->event->event_type = $data['event_type'];
        $this->event->start_datetime = $data['start_datetime'];
        $this->event->end_datetime = $data['end_datetime'];
        $this->event->status = 'pending';
        $this->event->created_by = $user_id;

        if($this->event->create()) {
            return ['success' => true, 'message' => 'Event created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create event'];
    }

    public function updateEvent($id, $data) {
        $this->event->id = $id;
        $this->event->title = $data['title'];
        $this->event->description = $data['description'];
        $this->event->venue = $data['venue'];
        $this->event->event_type = $data['event_type'];
        $this->event->start_datetime = $data['start_datetime'];
        $this->event->end_datetime = $data['end_datetime'];
        $this->event->status = $data['status'];

        if($this->event->update()) {
            return ['success' => true, 'message' => 'Event updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update event'];
    }

    public function deleteEvent($id) {
        $this->event->id = $id;
        if($this->event->delete()) {
            return ['success' => true, 'message' => 'Event deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete event'];
    }

    public function getAllEvents($page = 1) {
        $events = $this->event->getAll($page);
        $total = $this->event->getTotalCount();
        
        return [
            'events' => $events,
            'total' => $total,
            'current_page' => $page,
            'per_page' => 10,
            'total_pages' => ceil($total / 10)
        ];
    }

    public function getEvent($id) {
        return $this->event->getById($id);
    }

    public function getEventsByDateRange($start_date, $end_date) {
        return $this->event->getEventsByDateRange($start_date, $end_date);
    }

    public function updateEventStatus($id, $status) {
        $approver_id = $this->event->getUserIdFromSession($_SESSION['user_id']);
        if($this->event->updateStatus($id, $status, $approver_id)) {
            return ['success' => true, 'message' => 'Event status updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update event status'];
    }

    public function validateEventData($data) {
        $errors = [];

        if(empty($data['title'])) {
            $errors[] = "Title is required";
        }
        if(empty($data['venue'])) {
            $errors[] = "Venue is required";
        }
        if(empty($data['event_type']) || !in_array($data['event_type'], ['academic', 'sports', 'cultural', 'meeting', 'other'])) {
            $errors[] = "Invalid event type";
        }
        if(empty($data['start_datetime'])) {
            $errors[] = "Start date and time is required";
        }
        if(empty($data['end_datetime'])) {
            $errors[] = "End date and time is required";
        }
        if(!empty($data['start_datetime']) && !empty($data['end_datetime'])) {
            $start = strtotime($data['start_datetime']);
            $end = strtotime($data['end_datetime']);
            if($end <= $start) {
                $errors[] = "End date must be after start date";
            }
        }

        return $errors;
    }
}
?> 