<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUtilisateurs() {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParEmail($email) {
        $query = 'SELECT * FROM utilisateurs WHERE email = :email';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getEmail($email) {
        $query = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function getUtilisateurParId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addUser($email, $password, $role_id, $username) {
        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (email, mot_de_passe, role_id, nom_utilisateur) VALUES (:email, :mot_de_passe, :role_id, :nom_utilisateur)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom_utilisateur', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        // Commencer la requête SQL
        $sql = "UPDATE utilisateurs SET email = :email, role_id = :role_id, nom_utilisateur = :nom_utilisateur";
        $params = [
            ':id' => $id,
            ':email' => $email,
            ':role_id' => $role_id,
            ':nom_utilisateur' => $username
        ];
    
        // Si un nouveau mot de passe est fourni, l'ajouter à la requête
        if (!empty($password)) {
            $sql .= ", mot_de_passe = :mot_de_passe";
            $params[':mot_de_passe'] = password_hash($password, PASSWORD_DEFAULT);
        }
    
        // Terminer la requête SQL
        $sql .= " WHERE id = :id";
    
        // Préparer et exécuter la requête
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
    }
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    function getUsernames($db, $userIds) {
        if (empty($userIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $query = "SELECT id, nom_utilisateur FROM utilisateurs WHERE id IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($userIds);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $results;
    }
    
}

class User2 {
    private $conn;
    private $table = 'utilisateurs';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password, $role) {
        $query = "INSERT INTO " . $this->table . " (nom_utilisateur, email, mot_de_passe, role) VALUES (:nom_utilisateur, :email, :mot_de_passe, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom_utilisateur', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateUserProfile($userId, $username, $email, $newPassword = null) {
        if ($newPassword !== null) {
            $query = "UPDATE " . $this->table . " SET nom_utilisateur = :nom_utilisateur, email = :email, mot_de_passe = :mot_de_passe WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':mot_de_passe', $hashedPassword);
        } else {
            $query = "UPDATE " . $this->table . " SET nom_utilisateur = :nom_utilisateur, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':nom_utilisateur', $username);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
    public function sendFriendRequest($sender_id, $receiver_id) {
        $query = "INSERT INTO friend_requests (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':receiver_id', $receiver_id);
        return $stmt->execute();
    }

    public function respondFriendRequest($request_id, $status) {
        $query = "UPDATE friend_requests SET status = :status WHERE id = :request_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':request_id', $request_id);
        return $stmt->execute();
    }

    public function getFriendRequests($user_id) {
        $query = "SELECT * FROM friend_requests WHERE receiver_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFriends($user_id) {
        $query = "SELECT u.id, u.nom_utilisateur, fr.id as request_id
                  FROM friend_requests fr
                  JOIN utilisateurs u ON (fr.sender_id = u.id OR fr.receiver_id = u.id)
                  WHERE (fr.sender_id = :user_id OR fr.receiver_id = :user_id)
                  AND fr.status = 'accepted'
                  AND u.id != :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserByUsername($username) {
        $query = "SELECT * FROM utilisateurs WHERE nom_utilisateur = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function removeFriend($request_id) {
        $query = "DELETE FROM friend_requests WHERE id = :request_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        return $stmt->execute();
    }
    public function setResetToken($userId, $token) {
        $query = "UPDATE utilisateurs SET reset_token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function getUserByResetToken($token) {
        $query = "SELECT * FROM utilisateurs WHERE reset_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE utilisateurs SET mot_de_passe = :password, reset_token = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}
