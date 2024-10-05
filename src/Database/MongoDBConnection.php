<?php
namespace Database;

use Exception;
use MongoDB\Client;

class MongoDBConnection {
    private $mongoClient;
    private $mongoCollection;

    public function __construct() {
        $uri = 'mongodb+srv://AbduUSDI:heroku123456@abdurahmanusdi.lc9y4uk.mongodb.net';
        $databaseName = 'tpunn_quizz_score';

        try {
            $this->mongoClient = new Client($uri);
            $this->mongoCollection = $this->mongoClient->selectDatabase($databaseName)->scores;
        } catch (Exception $erreur) {
            error_log("Erreur de connexion à MongoDB : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données MongoDB");
        }
    }
    public function getCollection($collectionName) {
        return $this->mongoClient->selectDatabase('tpunn_quizz_score')->selectCollection($collectionName);
    }
    public function getScoresParents() {
        try {
            $collection = $this->mongoClient->selectDatabase('tpunn_quizz_score')->scores;
            
            $cursor = $collection->find(
                [],
                [
                    'sort' => ['score' => -1],
                    'limit' => 10
                ]
            );
            
            $scores = [];
            foreach ($cursor as $document) {
                $scores[] = [
                    'user_id' => $document['user_id'] ?? null,
                    'score' => $document['score'] ?? 0,
                    'total_score' => $document['total_score'] ?? 0
                ];
            }
            
            return $scores;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des scores : " . $e->getMessage());
            return [];
        }
    }
}