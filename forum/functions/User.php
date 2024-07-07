<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password, $role) {
        $query = "INSERT INTO " . $this->table . " (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUsernames($db, $userIds) {
        $usernames = [];
        $in  = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $db->prepare("SELECT id, username FROM users WHERE id IN ($in)");
        $stmt->execute($userIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usernames[$row['id']] = $row['username'];
        }
        return $usernames;
    }
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateProfile($userId, $username, $email, $newPassword = null) {
        if ($newPassword !== null) {
            $query = "UPDATE " . $this->table . " SET username = :username, email = :email, password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $query = "UPDATE " . $this->table . " SET username = :username, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
}
