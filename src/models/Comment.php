<?php
namespace Models;

use PDO;

class Comment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    // c = comments Table  ;   u = utilisateurs   ;
    public function getApprovedCommentsByGuideId($guide_id) {
        $query = "SELECT c.*, u.nom_utilisateur FROM commentaires c JOIN utilisateurs u ON c.user_id = u.id WHERE c.guide_id = :guide_id AND c.approuve = 1 ORDER BY c.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':guide_id', $guide_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($guide_id, $user_id, $contenu) {
        $query = "INSERT INTO commentaires (guide_id, user_id, contenu, approuve) VALUES (:guide_id, :user_id, :contenu, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':guide_id', $guide_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }
    public function approveComment($comment_id) {
        $query = "UPDATE commentaires SET approuve = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $comment_id);
        return $stmt->execute();
    }
    
    public function deleteComment($comment_id) {
        $query = "DELETE FROM commentaires WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $comment_id);
        return $stmt->execute();
    }
    public function getPendingComments() {
        $query = "SELECT c.*, u.nom_utilisateur, g.titre as guide_titre FROM commentaires c JOIN utilisateurs u ON c.user_id = u.id JOIN guides g ON c.guide_id = g.id WHERE c.approuve = 0 ORDER BY c.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getApprovedComments() {
        $query = "SELECT c.*, u.nom_utilisateur, g.titre as guide_titre FROM commentaires c JOIN utilisateurs u ON c.user_id = u.id JOIN guides g ON c.guide_id = g.id WHERE c.approuve = 1 ORDER BY c.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCommentById($id) {
        $sql = "SELECT * FROM commentaires WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateComment($id, $content) {
        $sql = "UPDATE commentaires SET contenu = :content WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'content' => $content
        ]);
    }
} 