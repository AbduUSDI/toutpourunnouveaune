<?php
class AvisMedicaux {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDerniersAvis($limit = 5) {
        $query = "SELECT * FROM conseils_medicaux ORDER BY date_creation DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM conseils_medicaux');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($titre, $contenu, $medecin_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO conseils_medicaux (titre, contenu, medecin_id) VALUES (?, ?, ?)");
            $result = $stmt->execute([$titre, $contenu, $medecin_id]);
            return $result;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la création de l'avis médical : " . $erreur->getMessage());
            return false;
        }
    }

    public function update($id, $titre, $contenu) {
        try {
            $stmt = $this->db->prepare("UPDATE conseils_medicaux SET titre = ?, contenu = ? WHERE id = ?");
            $result = $stmt->execute([$titre, $contenu, $id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'avis médical : " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM conseils_medicaux WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'avis médical : " . $e->getMessage());
            return false;
        }
    }
}