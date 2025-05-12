<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
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
    $stmt = $this->conn->prepare("SELECT u.*, r.role_name FROM user u JOIN role r ON u.role_id = r.role_id WHERE u.email = ?");
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
        'name' => $user['first_name'] . ' ' . $user['last_name']
    ];
}


    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT u.*, r.role_name FROM user u JOIN role r ON u.role_id = r.role_id");
        return $stmt->fetchAll();
    }

    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM user WHERE user_id = ?");
        return $stmt->execute([$id]);
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
