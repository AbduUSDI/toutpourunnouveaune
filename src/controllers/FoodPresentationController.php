<?php
namespace Controllers;

use Models\FoodPresentation;

class FoodPresentationController {
    private $foodPresentation;

    public function __construct(FoodPresentation $foodPresentation) {
        $this->foodPresentation = $foodPresentation;
    }

    // Récupérer toutes les présentations alimentaires
    public function getAllPresentations() {
        return $this->foodPresentation->getAll();
    }

    // Créer une nouvelle présentation alimentaire
    public function createPresentation($titre, $contenu, $groupe_age, $medecin_id) {
        if ($this->foodPresentation->create($titre, $contenu, $groupe_age, $medecin_id)) {
            return "Présentation alimentaire créée avec succès.";
        } else {
            return "Erreur lors de la création de la présentation alimentaire.";
        }
    }

    // Mettre à jour une présentation alimentaire existante
    public function updatePresentation($id, $titre, $contenu, $groupe_age) {
        if ($this->foodPresentation->update($id, $titre, $contenu, $groupe_age)) {
            return "Présentation alimentaire mise à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour de la présentation alimentaire.";
        }
    }

    // Supprimer une présentation alimentaire
    public function deletePresentation($id) {
        if ($this->foodPresentation->delete($id)) {
            return "Présentation alimentaire supprimée avec succès.";
        } else {
            return "Erreur lors de la suppression de la présentation alimentaire.";
        }
    }
}
