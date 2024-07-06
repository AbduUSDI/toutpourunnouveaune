<?php
class Tracking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getTracking() {
        $query = "SELECT * FROM suivi_quotidien ORDER BY date_creation";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($titre, $contenu, $groupe_age, $medecin_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO suivi_quotidien (titre, contenu, groupe_age, medecin_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$titre, $contenu, $groupe_age, $medecin_id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la création du suivi quotidien : " . $erreur->getMessage());
            return false;
        }
    }

    public function update($id, $titre, $contenu, $groupe_age) {
        try {
            $stmt = $this->db->prepare("UPDATE suivi_quotidien SET titre = ?, contenu = ?, groupe_age = ? WHERE id = ?");
            $result = $stmt->execute([$titre, $contenu, $groupe_age, $id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la mise à jour du suivi quotidien : " . $erreur->getMessage());
            return false;
        }
    }

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