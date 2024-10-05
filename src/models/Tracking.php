<?php
namespace Models;

use PDO;
use PDOException;

class Tracking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupération de tous les suivis quotidiens
    public function getTracking() {
        $query = "SELECT * FROM suivi_quotidien ORDER BY date_creation";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Création d'un nouveau suivi quotidien
    public function create($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes) {
        try {
            $date_creation = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare("INSERT INTO suivi_quotidien (utilisateur_id, date, heure_repas, duree_repas, heure_change, medicament, notes, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes, $date_creation]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la création du suivi quotidien : " . $erreur->getMessage());
            return false;
        }
    }

    // Mise à jour d'un suivi quotidien existant
    public function update($id, $utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes) {
        try {
            $stmt = $this->db->prepare("UPDATE suivi_quotidien SET utilisateur_id = ?, date = ?, heure_repas = ?, duree_repas = ?, heure_change = ?, medicament = ?, notes = ? WHERE id = ?");
            $result = $stmt->execute([$utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes, $id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la mise à jour du suivi quotidien : " . $erreur->getMessage());
            return false;
        }
    }

    // Suppression d'un suivi quotidien
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM suivi_quotidien WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la suppression du quotidien : " . $erreur->getMessage());
            return false;
        }
    }
}
