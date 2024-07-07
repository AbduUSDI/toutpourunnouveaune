<?php
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
        $query = "SELECT r.id, r.body, r.created_at, u.username as author FROM " . $this->table . " r JOIN users u ON r.user_id = u.id WHERE r.thread_id = :thread_id ORDER BY r.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':thread_id', $threadId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateResponse($responseId, $body) {
        $query = "UPDATE " . $this->table . " SET body = :body WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $responseId);
        $stmt->bindParam(':body', $body);
        if ($stmt->execute()) {
            return true;
        }
        return false;
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
}