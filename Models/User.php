<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getUserById($userId) {
        $stmt = $this->conn->prepare("
            SELECT u.*, r.*, s.*
            FROM user u 
            JOIN role r ON u.role_id = r.role_id 
            JOIN user_status s ON u.status_id = s.status_id
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE first_name = ?");
        $stmt->execute([$username]);
        return $stmt->rowCount() > 0;
    }

    public function usernamePasswordExists($username, $password) {
        $stmt = $this->conn->prepare("SELECT password FROM user WHERE first_name = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function isDuplicateUsernamePassword($username, $hashedPassword) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE first_name = ? AND password = ?");
        $stmt->execute([$username, $hashedPassword]);
        return $stmt->fetch() !== false;
    }

    public function register($name, $email, $password, $roleId) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (first_name, last_name, email, password, role_id, status_id) VALUES (?, '', ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $email, $hashedPassword, $roleId]);
    }

  public function login($email, $password) {
  $stmt = $this->conn->prepare("
        SELECT u.*, r.role_name, s.status_name 
        FROM user u 
        JOIN role r ON u.role_id = r.role_id 
        JOIN user_status s ON u.status_id = s.status_id
        WHERE u.email = ?
    ");   
  $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("No user found for email: $email");
        return ['error' => 'Invalid email or password.'];
    }

    if (!password_verify($password, $user['password'])) {
        error_log("Wrong password for: $email");
        return ['error' => 'Invalid email or password.'];
    }
    
    return [
        'id' => $user['user_id'],
        'role' => $user['role_name'],
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'status' => $user['status_name']
    ];
}


    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT u.*, r.role_name FROM user u JOIN role r ON u.role_id = r.role_id");
        return $stmt->fetchAll();
    }
    
    /**
    * Get all users with pending status
    */
    public function getPendingUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.user_id, u.first_name, u.last_name, u.email, u.created_at
                FROM user u 
                JOIN user_status s ON u.status_id = s.status_id
                WHERE s.status_name = 'Pending'
                ORDER BY u.created_at DESC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get pending users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update user status
     */
    public function updateUserStatus($userId, $status) {
        try {
            // Get status ID based on status name
            $statusStmt = $this->conn->prepare("
                SELECT status_id FROM user_status WHERE status_name = ?
            ");
            $statusStmt->execute([$status]);
            $statusId = $statusStmt->fetchColumn();

            if (!$statusId) {
                return false;
            }

            // Update user status
            $updateStmt = $this->conn->prepare("
                UPDATE user SET status_id = ? WHERE user_id = ?
            ");

            return $updateStmt->execute([$statusId, $userId]);
        } catch (PDOException $e) {
            error_log("Failed to update user status: " . $e->getMessage());
            return false;
        }
    }

 public function deleteUser($id) {
    try {
        // First, delete bookings made by this user
        // Note: The column name in booking table is 'customer_id' not 'user_id'
        $stmtUserBookings = $this->conn->prepare("DELETE FROM booking WHERE customer_id = ?");
        $stmtUserBookings->execute([$id]);
        
        // Check if user has associated charge points
        $stmtChargers = $this->conn->prepare("SELECT charger_id FROM charge_point WHERE user_id = ?");
        $stmtChargers->execute([$id]);
        $chargers = $stmtChargers->fetchAll(PDO::FETCH_COLUMN);
        
        // If user has chargers, delete bookings for those chargers first
        if (!empty($chargers)) {
            foreach ($chargers as $chargerId) {
                // Delete bookings for this charger
                $stmtChargerBookings = $this->conn->prepare("DELETE FROM booking WHERE charge_id = ?");
                $stmtChargerBookings->execute([$chargerId]);
            }
            
            // Then delete the chargers themselves
            $stmtChargers = $this->conn->prepare("DELETE FROM charge_point WHERE user_id = ?");
            $stmtChargers->execute([$id]);
        }
        
        // Finally delete the user
        $stmtUser = $this->conn->prepare("DELETE FROM user WHERE user_id = ?");
        return $stmtUser->execute([$id]);
        
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        throw $e;
    }
}

    public function setStatus($id, $statusId) {
        $stmt = $this->conn->prepare("UPDATE user SET status_id = ? WHERE user_id = ?");
        return $stmt->execute([$statusId, $id]);
    }

    public function getRoleOptions() {
        return $this->conn->query("SELECT * FROM role")->fetchAll();
    }
}
?>
