<?php
namespace Models;

use PDO;

class Profile {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProfileByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM profils WHERE utilisateur_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveProfile($userId, $firstName, $lastName, $birthDate, $biography, $imageName = null) {
        // Vérifier si le profil existe
        $query = "SELECT COUNT(*) FROM profils WHERE utilisateur_id = :utilisateur_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $userId);
        $stmt->execute();
        $exists = $stmt->fetchColumn();
    
        if ($exists) {
            // Mettre à jour le profil existant
            $query = "UPDATE profils SET prenom = :prenom, nom = :nom, date_naissance = :date_naissance, biographie = :biographie";
            if ($imageName) {
                $query .= ", photo_profil = :photo_profil";
            }
            $query .= " WHERE utilisateur_id = :utilisateur_id";
        } else {
            // Créer un nouveau profil
            $query = "INSERT INTO profils (utilisateur_id, prenom, nom, date_naissance, biographie, photo_profil) VALUES (:utilisateur_id, :prenom, :nom, :date_naissance, :biographie, :photo_profil)";
        }
    
        $stmt = $this->db->prepare($query);
    
        $stmt->bindParam(':prenom', $firstName);
        $stmt->bindParam(':nom', $lastName);
        $stmt->bindParam(':date_naissance', $birthDate);
        $stmt->bindParam(':biographie', $biography);
        if ($imageName) {
            $stmt->bindParam(':photo_profil', $imageName);
        }
        $stmt->bindParam(':utilisateur_id', $userId);
    
        return $stmt->execute();
    }
    
    public function updateProfilePicture($userId, $imageName) {
        $query = "UPDATE profils SET photo_profil = :photo_profil WHERE utilisateur_id = :utilisateur_id";
    
        $stmt = $this->db->prepare($query);
    
        $stmt->bindParam(':photo_profil', $imageName);
        $stmt->bindParam(':utilisateur_id', $userId);
    
        return $stmt->execute();
    }    
}