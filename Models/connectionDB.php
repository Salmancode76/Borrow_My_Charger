<?php

class connectionDB {
    private $host = 'localhost';
    private $dbname = 'charger_db';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
?>
