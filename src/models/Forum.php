<?php
namespace Models;

use PDO;

class Forum {
    private $conn;
    private $table = 'threads';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addThread($title, $body, $user_id) {
        $query = "INSERT INTO " . $this->table . " (title, body, user_id, created_at) VALUES (:title, :body, :user_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getActiveThreads($mongoClient) {
        $viewsCollection = $mongoClient->getCollection('views');
        $threadsCollection = $mongoClient->getCollection('threads');
        
        $activeThreads = $viewsCollection->find([], [
            'sort' => ['views' => -1],
            'limit' => 5
        ]);
    
        $activeThreadsArray = [];
        foreach ($activeThreads as $activeThread) {
            $threadId = $activeThread['thread_id'];
            $thread = $threadsCollection->findOne(['_id' => $threadId]);
            if ($thread) {
                $activeThreadsArray[] = $thread;
            }
        }
        return $activeThreadsArray;
    }
    public function getThreads($limit = 10) {
        $query = "SELECT t.id, t.title, t.body, t.created_at, u.nom_utilisateur as author 
                  FROM " . $this->table . " t 
                  JOIN utilisateurs u ON t.user_id = u.id 
                  ORDER BY t.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getThreadById($id) {
        $query = "SELECT threads.*, utilisateurs.nom_utilisateur AS author
                  FROM threads
                  JOIN utilisateurs ON threads.user_id = utilisateurs.id
                  WHERE threads.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateThread($id, $title, $body) {
        $sql = "UPDATE threads SET title = :title, body = :body WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteThread($threadId) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $threadId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getThreadsByUserId($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}