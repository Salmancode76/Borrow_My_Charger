<?php
class connectionDB {
    private $host = '127.0.0.1';
    private $port = '3307';
    private $dbname = 'charger_db';
    private $user = 'root';
    private $pass = ''; 

    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
?>
