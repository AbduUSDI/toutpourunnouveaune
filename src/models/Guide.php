<?php
namespace Models;

use PDO;

class Guide {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createGuide($titre, $contenu, $auteur_id) {
        $query = "INSERT INTO guides (titre, contenu, auteur_id) VALUES (:titre, :contenu, :auteur_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':auteur_id', $auteur_id);
        return $stmt->execute();
    }

    public function getAllGuides() {
        $query = "SELECT g.*, u.nom_utilisateur as auteur_nom FROM guides g JOIN utilisateurs u ON g.auteur_id = u.id ORDER BY g.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuideById($id) {
        $query = "SELECT g.*, u.nom_utilisateur as auteur_nom FROM guides g JOIN utilisateurs u ON g.auteur_id = u.id WHERE g.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateGuide($id, $titre, $contenu) {
        $query = "UPDATE guides SET titre = :titre, contenu = :contenu, date_mise_a_jour = CURRENT_TIMESTAMP() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }

    public function deleteGuide($id) {
        $query = "DELETE FROM guides WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}