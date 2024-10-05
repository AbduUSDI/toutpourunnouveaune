<?php
namespace Controllers;

use Models\Response;

class ResponseController {
    private $response;

    public function __construct(Response $response) {
        $this->response = $response;
    }

    // Créer une nouvelle réponse
    public function createResponse($threadId, $userId, $body) {
        if ($this->response->createResponse($threadId, $userId, $body)) {
            return "Réponse ajoutée avec succès.";
        } else {
            return "Erreur lors de l'ajout de la réponse.";
        }
    }

    // Récupérer les réponses d'un thread spécifique
    public function getResponsesByThreadId($threadId) {
        return $this->response->getResponsesByThreadId($threadId);
    }

    // Mettre à jour une réponse existante
    public function updateResponse($id, $body) {
        if ($this->response->updateResponse($id, $body)) {
            return "Réponse mise à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour de la réponse.";
        }
    }

    // Supprimer une réponse
    public function deleteResponse($responseId) {
        if ($this->response->deleteResponse($responseId)) {
            return "Réponse supprimée avec succès.";
        } else {
            return "Erreur lors de la suppression de la réponse.";
        }
    }

    // Récupérer les réponses postées par un utilisateur spécifique
    public function getResponsesByUserId($userId) {
        return $this->response->getResponsesByUserId($userId);
    }
}
