<?php
include_once 'MongoDB.php';
include_once 'functions/Quiz.php';

header('Content-Type: application/json');

$mongoClient = new MongoDB();
$database = new Database();
$db = $database->connect();
$quizManager = new Quiz($db);

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quizId = $_POST['quiz_id'];
        $questions = $_POST['questions'];
        $score = 0;

        // Calculer le score
        foreach ($questions as $questionId => $answer) {
            $correctOption = $quizManager->getCorrectOption($questionId);
            if ($correctOption == $answer['answer']) {
                $score += 10;
            }
        }

        // Enregistrer le score dans MongoDB
        $collection = $mongoClient->getCollection('scores');
        $result = $collection->insertOne([
            'quiz_id' => $quizId,
            'user_id' => $_SESSION['user_id'], // Assurez-vous que l'utilisateur est connecté
            'score' => $score,
        ]);

        $response['success'] = true;
        $response['score'] = $score;
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);