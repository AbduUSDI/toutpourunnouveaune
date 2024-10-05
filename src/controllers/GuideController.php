<?php
namespace Controllers;

use Models\Guide;

class GuideController {
    private $guide;

    public function __construct(Guide $guide) {
        $this->guide = $guide;
    }

    // Créer un nouveau guide
    public function createGuide($titre, $contenu, $auteur_id) {
        if ($this->guide->createGuide($titre, $contenu, $auteur_id)) {
            return "Guide créé avec succès.";
        } else {
            return "Erreur lors de la création du guide.";
        }
    }

    // Récupérer tous les guides
    public function getAllGuides() {
        return $this->guide->getAllGuides();
    }

    // Récupérer un guide par son ID
    public function getGuideById($id) {
        $guide = $this->guide->getGuideById($id);
        if ($guide) {
            return $guide;
        } else {
            return "Guide introuvable.";
        }
    }

    // Mettre à jour un guide existant
    public function updateGuide($id, $titre, $contenu) {
        if ($this->guide->updateGuide($id, $titre, $contenu)) {
            return "Guide mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du guide.";
        }
    }

    // Supprimer un guide
    public function deleteGuide($id) {
        if ($this->guide->deleteGuide($id)) {
            return "Guide supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression du guide.";
        }
    }
}
