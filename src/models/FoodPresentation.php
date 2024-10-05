<?php
namespace Models;

use PDO;
use PDOException;

class FoodPresentation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query('SELECT * FROM presentations_alimentaires');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($titre, $contenu, $groupe_age, $medecin_id) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO presentations_alimentaires (titre, contenu, groupe_age, medecin_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$titre, $contenu, $groupe_age, $medecin_id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la création de la présentation alimentaire : " . $erreur->getMessage());
            return false;
        }
    }

    public function update($id, $titre, $contenu, $groupe_age) {
        try {
            $stmt = $this->conn->prepare("UPDATE presentations_alimentaires SET titre = ?, contenu = ?, groupe_age = ? WHERE id = ?");
            $result = $stmt->execute([$titre, $contenu, $groupe_age, $id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la présentation alimentaire : " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM presentations_alimentaires WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la présentation alimentaire : " . $e->getMessage());
            return false;
        }
    }
}