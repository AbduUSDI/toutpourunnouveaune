<?php
namespace Controllers;

use Models\User;

class UserController {
    private $user;
    private $db;

    public function __construct($db, User $user) {
        $this->db = $db;
        $this->user = $user;
    }

    // Récupérer tous les utilisateurs
    public function getAllUtilisateurs() {
        return $this->user->getAllUtilisateurs();
    }

    // Récupérer un utilisateur par email
    public function getUtilisateurParEmail($email) {
        return $this->user->getUtilisateurParEmail($email);
    }

    // Vérifier si un email existe déjà
    public function emailExists($email) {
        return $this->user->getEmail($email);
    }

    // Récupérer un utilisateur par son ID
    public function getUtilisateurParId($id) {
        return $this->user->getUtilisateurParId($id);
    }

    // Ajouter un nouvel utilisateur
    public function addUser($email, $password, $role_id, $username) {
        return $this->user->addUser($email, $password, $role_id, $username);
    }

    // Mettre à jour un utilisateur existant
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        $this->user->updateUser($id, $email, $role_id, $username, $password);
        return "Utilisateur mis à jour avec succès.";
    }

    // Supprimer un utilisateur
    public function deleteUser($id) {
        $this->user->deleteUser($id);
        return "Utilisateur supprimé avec succès.";
    }

    // Mettre à jour le mot de passe d'un utilisateur
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($this->user->updatePassword($userId, $hashedPassword)) {
            return "Mot de passe mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du mot de passe.";
        }
    }

    // Récupérer les noms d'utilisateur pour une liste d'IDs
    public function getUsernames($userIds) {
        return $this->user->getUsernames($this->db, $userIds);
    }
}
