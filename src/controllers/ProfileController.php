<?php
namespace Controllers;

use Models\Profile;

class ProfileController {
    private $profile;

    public function __construct(Profile $profile) {
        $this->profile = $profile;
    }

    // Récupérer le profil d'un utilisateur par ID
    public function getProfileByUserId($userId) {
        $profile = $this->profile->getProfileByUserId($userId);
        if ($profile) {
            return $profile;
        } else {
            return "Profil introuvable.";
        }
    }

    // Enregistrer ou mettre à jour un profil
    public function saveProfile($userId, $firstName, $lastName, $birthDate, $biography, $imageName = null) {
        if ($this->profile->saveProfile($userId, $firstName, $lastName, $birthDate, $biography, $imageName)) {
            return "Profil enregistré avec succès.";
        } else {
            return "Erreur lors de l'enregistrement du profil.";
        }
    }

    // Mettre à jour l'image de profil
    public function updateProfilePicture($userId, $imageName) {
        if ($this->profile->updateProfilePicture($userId, $imageName)) {
            return "Photo de profil mise à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour de la photo de profil.";
        }
    }
}
