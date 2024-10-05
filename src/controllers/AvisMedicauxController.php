<?php
namespace Controllers;

use Models\AvisMedicaux;

class AvisMedicauxController {
    private $avisMedicaux;

    public function __construct(AvisMedicaux $avisMedicaux) {
        $this->avisMedicaux = $avisMedicaux;
    }

    // Récupérer les derniers avis
    public function getDerniersAvis($limit = 5) {
        return $this->avisMedicaux->getDerniersAvis($limit);
    }

    // Récupérer un avis par son ID
    public function getParId($id) {
        return $this->avisMedicaux->getParId($id);
    }

    // Récupérer tous les avis
    public function getAllAvis() {
        return $this->avisMedicaux->getAll();
    }

    // Créer un nouvel avis médical
    public function createAvis($titre, $contenu, $medecin_id) {
        if ($this->avisMedicaux->create($titre, $contenu, $medecin_id)) {
            return "Avis médical créé avec succès.";
        } else {
            return "Erreur lors de la création de l'avis médical.";
        }
    }

    // Mettre à jour un avis médical existant
    public function updateAvis($id, $titre, $contenu) {
        if ($this->avisMedicaux->update($id, $titre, $contenu)) {
            return "Avis médical mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour de l'avis médical.";
        }
    }

    // Supprimer un avis médical
    public function deleteAvis($id) {
        if ($this->avisMedicaux->delete($id)) {
            return "Avis médical supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression de l'avis médical.";
        }
    }
}
