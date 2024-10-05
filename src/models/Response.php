<?php
namespace Models;

use PDO;

class Response {
    private $conn;
    private $table = 'responses';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createResponse($threadId, $userId, $body) {
        $query = "INSERT INTO " . $this->table . " (thread_id, user_id, body) VALUES (:thread_id, :user_id, :body)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':thread_id', $threadId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':body', $body);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getResponsesByThreadId($threadId) {
        $query = "SELECT r.id, r.body, r.created_at, u.nom_utilisateur as author FROM " . $this->table . " r JOIN utilisateurs u ON r.user_id = u.id WHERE r.thread_id = :thread_id ORDER BY r.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':thread_id', $threadId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateResponse($id, $body) {
        $sql = "UPDATE responses SET body = :body WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function deleteResponse($responseId) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $responseId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getResponsesByUserId($userId) {
        $query = "SELECT r.id, r.body, r.created_at, t.title as thread_title 
                  FROM " . $this->table . " r 
                  JOIN threads t ON r.thread_id = t.id 
                  WHERE r.user_id = :user_id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}