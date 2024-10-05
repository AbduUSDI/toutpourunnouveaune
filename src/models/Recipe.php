<?php
namespace Models;

use PDO;
use PDOException;
class Recipe {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query('SELECT * FROM recettes');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($titre, $ingredients, $instructions, $auteur_id) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO recettes (titre, ingredients, instructions, auteur_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$titre, $ingredients, $instructions, $auteur_id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la recette : " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $titre, $ingredients, $instructions) {
        try {
            $stmt = $this->conn->prepare("UPDATE recettes SET titre = ?, ingredients = ?, instructions = ? WHERE id = ?");
            $result = $stmt->execute([$titre, $ingredients, $instructions, $id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la recette : " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM recettes WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la recette : " . $e->getMessage());
            return false;
        }
    }
}