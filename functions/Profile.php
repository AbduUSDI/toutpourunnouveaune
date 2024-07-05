<?php
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

    public function updateProfile($userId, $prenom, $nom, $date_naissance, $biographie, $photo_profil) {
        $stmt = $this->db->prepare("INSERT INTO profils (utilisateur_id, prenom, nom, date_naissance, biographie, photo_profil) 
                                    VALUES (?, ?, ?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE 
                                    prenom = VALUES(prenom), 
                                    nom = VALUES(nom), 
                                    date_naissance = VALUES(date_naissance), 
                                    biographie = VALUES(biographie), 
                                    photo_profil = VALUES(photo_profil)");
        return $stmt->execute([$userId, $prenom, $nom, $date_naissance, $biographie, $photo_profil]);
    }
}