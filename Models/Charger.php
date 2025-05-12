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

    // use user id 
    public function getChargerByChargerID($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM `charge_point`
                WHERE `charger_id` = :charger_id
                ORDER BY `charger_id` DESC
                LIMIT 1
            ");

            $stmt->bindParam(':charger_id', $id);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result; 
        } catch (PDOException $exc) {
            die("Select failed: " . $exc->getMessage());
        }
    }

    
    //use user id 
    public function getChargerByID($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM `charge_point`
                WHERE `user_id` = :user_id
                ORDER BY `charger_id` DESC
                LIMIT 1
            ");

            $stmt->bindParam(':user_id', $id);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result; 
        } catch (PDOException $exc) {
            die("Select failed: " . $exc->getMessage());
        }
    }
public function updateCharger($param) {
    try {
        $stmt = $this->conn->prepare("
            UPDATE `charge_point` SET 
                `charge_name` = :charge_name,
                `cost` = :cost,
                `availability` = :availability,
                `latitude` = :latitude,
                `longitude` = :longitude,
                `picture` = :picture,
                `Location` = :Location,
                `available_from` = :available_from,
                `available_to` = :available_to
            WHERE `charger_id` = :charger_id
        ");

        $stmt->bindParam(':charge_name', $param['name']);
        $stmt->bindParam(':cost', $param['price']);
        $stmt->bindParam(':availability', $param['availability']);
        $stmt->bindParam(':latitude', $param['latitude']);
        $stmt->bindParam(':longitude', $param['longitude']);
        $stmt->bindParam(':picture', $param['image']);
        $stmt->bindParam(':Location', $param['Location']);
        $stmt->bindParam(':available_from', $param['available_from']);
        $stmt->bindParam(':available_to', $param['available_to']);
        $stmt->bindParam(':charger_id', $param['charger_id']);

        return $stmt->execute();
        
    } catch (PDOException $ex) {
        die("Update failed: " . $ex->getMessage());
    }
}





    public function insert_charger($param) {
    try {
        $stmt = $this->conn->prepare("
            INSERT INTO charge_point 
            (charge_name, availability, cost, latitude, longitude, user_id, picture,Location,available_from,available_to)
            VALUES 
            (:charge_name, :availability, :cost, :latitude, :longitude, :user_id, :picture, :Location,:available_from,:available_to)
        ");

        $charge_name = $param['name'];
        $availability = $param['availability'];
        $cost = $param['price'];
        $latitude = $param['latitude'];
        $longitude = $param['longitude'];
        $user_id = $_SESSION['user_id']; 
        $picture = $param['image'];
        $location = $param['Location'];
        $avaFrom = $param['$avaFrom'];
        $avaTo = $param['$avaTo'];

        $stmt->bindParam(':charge_name', $charge_name);
        $stmt->bindParam(':availability', $availability);
        $stmt->bindParam(':cost', $cost);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':user_id', $user_id); 
        $stmt->bindParam(':picture', $picture);
        $stmt->bindParam(':Location', $location);
        $stmt->bindParam(':available_from',$avaFrom);
        $stmt->bindParam(':available_to', $avaTo);

        return $stmt->execute();
    } catch (PDOException $e) {
        die("Insert failed: " . $e->getMessage());
    }
}

}
?>
