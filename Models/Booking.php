<?php
/* 
 * Booking model for managing charge point booking operations
 */

require_once 'connectionDB.php';

class Booking {
    private $conn;

    public function __construct() {
        $db = new connectionDB(); 
        $this->conn = $db->connect(); 
    }

    /**
     * Create a new booking
     */
    public function createBooking($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO booking 
                (customer_id, charge_id, date, status)
                VALUES 
                (:customer_id, :charge_id, :date, :status)
            ");

            $status = "Pending"; // Default status for new bookings
            
            $stmt->bindParam(':customer_id', $data['customer_id']);
            $stmt->bindParam(':charge_id', $data['charge_id']);
            $stmt->bindParam(':date', $data['date']);
            $stmt->bindParam(':status', $status);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            die("Booking creation failed: " . $e->getMessage());
        }
    }

    /**
     * Update booking status (Approve/Decline)
     */
    public function updateBookingStatus($booking_id, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE booking 
                SET status = :status 
                WHERE booking_id = :booking_id
            ");
            
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':booking_id', $booking_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Status update failed: " . $e->getMessage());
        }
    }

    /**
     * Get bookings by customer ID with pagination
     */
    public function getBookingsByCustomer($customer_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT b.*, c.charge_name, c.location, c.cost, u.first_name, u.last_name, u.email
                FROM booking b
                JOIN charge_point c ON b.charge_id = c.charger_id
                JOIN user u ON c.user_id = u.user_id
                WHERE b.customer_id = :customer_id
                ORDER BY b.date DESC
            ");
            
            $stmt->bindParam(':customer_id', $customer_id);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Get bookings by charger owner ID with pagination
     */
    public function getBookingsByChargerOwner($owner_id) {
        try {            
            $stmt = $this->conn->prepare("
                SELECT b.*, c.charge_name, c.location, c.cost, 
                       u.first_name as customer_first_name, u.last_name as customer_last_name, u.email as customer_email
                FROM booking b
                JOIN charge_point c ON b.charge_id = c.charger_id
                JOIN user u ON b.customer_id = u.user_id
                WHERE c.user_id = :owner_id
                ORDER BY b.date DESC
            ");
            
            $stmt->bindParam(':owner_id', $owner_id);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Count total bookings for pagination (by customer)
     */
    public function countCustomerBookings($customer_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total FROM booking 
                WHERE customer_id = :customer_id
            ");
            
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            die("Count query failed: " . $e->getMessage());
        }
    }

    /**
     * Count total bookings for pagination (by owner)
     */
    public function countOwnerBookings($owner_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM booking b
                JOIN charge_point c ON b.charge_id = c.charger_id
                WHERE c.user_id = :owner_id
            ");
            
            $stmt->bindParam(':owner_id', $owner_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            die("Count query failed: " . $e->getMessage());
        }
    }

    /**
     * Get a specific booking by ID
     */
    public function getBookingById($booking_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT b.*, c.charge_name, c.location, c.cost, c.user_id as owner_id,
                       o.first_name as owner_first_name, o.last_name as owner_last_name, o.email as owner_email,
                       u.first_name as customer_first_name, u.last_name as customer_last_name, u.email as customer_email
                FROM booking b
                JOIN charge_point c ON b.charge_id = c.charger_id
                JOIN user o ON c.user_id = o.user_id
                JOIN user u ON b.customer_id = u.user_id
                WHERE b.booking_id = :booking_id
            ");
            
            $stmt->bindParam(':booking_id', $booking_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Check for booking availability (to prevent double bookings)
     */
    public function checkAvailability($charge_id, $date) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as booked
                FROM booking
                WHERE charge_id = :charge_id 
                AND date = :date
                AND status != 'Declined'
            ");
            
            $stmt->bindParam(':charge_id', $charge_id);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['booked'] == 0; // Returns true if available
        } catch (PDOException $e) {
            die("Availability check failed: " . $e->getMessage());
        }
    }

    /**
     * Send message to charger owner (stores in database)
     */
    public function sendMessage($data) {
        try {
            // Assuming you have a messages table
            $stmt = $this->conn->prepare("
                INSERT INTO messages 
                (booking_id, from_user_id, to_user_id, message, sent_at)
                VALUES 
                (:booking_id, :from_user_id, :to_user_id, :message, NOW())
            ");
            
            $stmt->bindParam(':booking_id', $data['booking_id']);
            $stmt->bindParam(':from_user_id', $data['from_user_id']);
            $stmt->bindParam(':to_user_id', $data['to_user_id']);
            $stmt->bindParam(':message', $data['message']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Message sending failed: " . $e->getMessage());
        }
    }

    /**
     * Get booking status updates (for AJAX polling)
     */
    public function getBookingStatusUpdates($customer_id, $last_check_time) {
        try {
            $stmt = $this->conn->prepare("
                SELECT booking_id, status, date
                FROM booking
                WHERE customer_id = :customer_id
                AND last_updated > :last_check_time
            ");
            
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':last_check_time', $last_check_time);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Status update query failed: " . $e->getMessage());
        }
    }
}