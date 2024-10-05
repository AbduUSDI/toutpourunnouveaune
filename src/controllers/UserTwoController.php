<?php
namespace Controllers;

use Models\UserTwo;

class UserTwoController {
    private $user;

    public function __construct(UserTwo $user) {
        $this->user = $user;
    }

    // Enregistrer un nouvel utilisateur
    public function register($username, $email, $password, $role) {
        if ($this->user->register($username, $email, $password, $role)) {
            return "Inscription réussie.";
        } else {
            return "Erreur lors de l'inscription.";
        }
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        return $this->user->getUserById($id);
    }

    // Mettre à jour le profil d'un utilisateur
    public function updateUserProfile($userId, $username, $email, $newPassword = null) {
        if ($this->user->updateUserProfile($userId, $username, $email, $newPassword)) {
            return "Profil mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du profil.";
        }
    }

    // Envoyer une demande d'ami
    public function sendFriendRequest($sender_id, $receiver_id) {
        if ($this->user->sendFriendRequest($sender_id, $receiver_id)) {
            return "Demande d'ami envoyée avec succès.";
        } else {
            return "Erreur lors de l'envoi de la demande d'ami.";
        }
    }

    // Répondre à une demande d'ami
    public function respondFriendRequest($request_id, $status) {
        if ($this->user->respondFriendRequest($request_id, $status)) {
            return "Réponse à la demande d'ami enregistrée avec succès.";
        } else {
            return "Erreur lors de la réponse à la demande d'ami.";
        }
    }

    // Récupérer les demandes d'amis en attente
    public function getFriendRequests($user_id) {
        return $this->user->getFriendRequests($user_id);
    }

    // Récupérer les amis d'un utilisateur
    public function getFriends($user_id) {
        return $this->user->getFriends($user_id);
    }
    // Récupérer le l'username d'un utilisateur
    public function getByUsername($username) {
        return $this->user->getUserByUsername($username);
    }
    // Supprimer un ami
    public function removeFriend($request_id) {
        if ($this->user->removeFriend($request_id)) {
            return "Ami supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression de l'ami.";
        }
    }

    // Définir un token de réinitialisation de mot de passe
    public function setResetToken($userId, $token) {
        if ($this->user->setResetToken($userId, $token)) {
            return "Token de réinitialisation défini avec succès.";
        } else {
            return "Erreur lors de la définition du token de réinitialisation.";
        }
    }

    // Récupérer un utilisateur par token de réinitialisation
    public function getUserByResetToken($token) {
        return $this->user->getUserByResetToken($token);
    }

    // Mettre à jour le mot de passe d'un utilisateur
    public function updatePassword($userId, $newPassword) {
        if ($this->user->updatePassword($userId, $newPassword)) {
            return "Mot de passe mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du mot de passe.";
        }
    }
}
