<?php
namespace Controllers;

use Models\Forum;
use MongoDB\Client as MongoClient;

class ForumController {
    private $forum;

    public function __construct(Forum $forum) {
        $this->forum = $forum;
    }

    // Ajouter un nouveau thread
    public function addThread($title, $body, $user_id) {
        if ($this->forum->addThread($title, $body, $user_id)) {
            return "Thread ajouté avec succès.";
        } else {
            return "Erreur lors de l'ajout du thread.";
        }
    }

    // Récupérer les threads actifs (en fonction des vues) avec MongoDB
    public function getActiveThreads(MongoClient $mongoClient) {
        $activeThreads = $this->forum->getActiveThreads($mongoClient);
        if ($activeThreads) {
            return $activeThreads;
        } else {
            return "Aucun thread actif trouvé.";
        }
    }

    // Récupérer un nombre limité de threads
    public function getThreads($limit = 10) {
        return $this->forum->getThreads($limit);
    }

    // Récupérer un thread par ID
    public function getThreadById($id) {
        $thread = $this->forum->getThreadById($id);
        if ($thread) {
            return $thread;
        } else {
            return "Thread introuvable.";
        }
    }

    // Mettre à jour un thread
    public function updateThread($id, $title, $body) {
        if ($this->forum->updateThread($id, $title, $body)) {
            return "Thread mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du thread.";
        }
    }

    // Supprimer un thread
    public function deleteThread($id) {
        if ($this->forum->deleteThread($id)) {
            return "Thread supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression du thread.";
        }
    }

    // Récupérer les threads créés par un utilisateur spécifique
    public function getThreadsByUserId($userId) {
        $threads = $this->forum->getThreadsByUserId($userId);
        if ($threads) {
            return $threads;
        } else {
            return "Aucun thread trouvé pour cet utilisateur.";
        }
    }
}
