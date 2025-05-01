<?php
require_once 'connectionDB.php';

class Charger {
    private $conn;

    public function __construct() {
        $db = new connectionDB(); 
        $this->conn = $db->connect(); 
    }

    public function getAllChargers() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM charge_point"); 
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }
}
?>
