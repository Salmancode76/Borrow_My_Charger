<?php
/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

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
    
     public function searchChargers($max_price = '' , $location = '') {
        try {
            $sql = "SELECT * FROM charge_point WHERE 1=1";  // Base query
            
            $params = [];  

            if (!empty($location)) {
            $sql .= " AND Location LIKE :location";
            $params[':location'] = $location . '%'; 
        }

            if (!empty($max_price)) {
                $sql .= " AND cost <= :max_price";  
                $params[':max_price'] = $max_price;  
            }
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 

        } catch (PDOException $e) {
            die("Database query failed: " . $e->getMessage());
        }
    }


    }

?>
