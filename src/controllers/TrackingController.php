<?php
namespace Controllers;

use Models\Tracking;

class TrackingController {
    private $tracking;

    public function __construct(Tracking $tracking) {
        $this->tracking = $tracking;
    }

    // Récupérer tous les suivis quotidiens
    public function getAllTracking() {
        return $this->tracking->getTracking();
    }

    // Créer un nouveau suivi quotidien
    public function createTracking($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes) {
        if ($this->tracking->create($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes)) {
            return "Suivi quotidien créé avec succès.";
        } else {
            return "Erreur lors de la création du suivi quotidien.";
        }
    }

    // Mettre à jour un suivi quotidien existant
    public function updateTracking($id, $utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes) {
        if ($this->tracking->update($id, $utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes)) {
            return "Suivi quotidien mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du suivi quotidien.";
        }
    }

    // Supprimer un suivi quotidien
    public function deleteTracking($id) {
        if ($this->tracking->delete($id)) {
            return "Suivi quotidien supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression du suivi quotidien.";
        }
    }
}
