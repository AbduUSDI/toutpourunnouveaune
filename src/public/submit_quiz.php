<?php
session_start();
require_once '../../vendor/autoload.php';

// Vérification CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Action non autorisée.');
}

$db = (new Database\DatabaseConnection())->connect();

$quiz = new \Models\Quiz($db);
$quizController = new \Controllers\QuizController($quiz);

$mongoClient = new \Database\MongoDBConnection();
$collection = $mongoClient->getCollection('scores');

$quiz_id = filter_input(INPUT_POST, 'quiz_id', FILTER_SANITIZE_NUMBER_INT);
$user_answers = $_POST['answers'] ?? [];

$score = $quizController->calculateScore($quiz_id, $user_answers);

$user_id = $_SESSION['user']['id'];

try {
    $existingUser = $collection->findOne(['user_id' => $user_id]);

if ($existingUser) {
    // Convertir total_score en entier avant l'addition
    $newTotalScore = intval($existingUser['total_score']) + $score;

    $quizScores = $existingUser['quiz_scores'];
    $quizFound = false;

    foreach ($quizScores as &$quizScore) {
        if ($quizScore['quiz_id'] == $quiz_id) {
            // Convertir le score en entier avant l'addition
            $quizScore['score'] = intval($quizScore['score']) + $score;
            $quizFound = true;
            break;
        }
    }

    if (!$quizFound) {
        $quizScores[] = ['quiz_id' => $quiz_id, 'score' => $score];
    }

    $collection->updateOne(
        ['user_id' => $user_id],
        [
            '$set' => [
                'total_score' => $newTotalScore,
                'quiz_scores' => $quizScores,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]
        ]
    );
} else {
    $collection->insertOne([
        'user_id' => $user_id,
        'total_score' => $score,
        'quiz_scores' => [
            ['quiz_id' => $quiz_id, 'score' => $score]
        ],
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ]);
}

} catch (Exception $e) {
    error_log('Erreur lors de la soumission du quiz : ' . $e->getMessage());
    die('Erreur lors de la soumission du quiz. Veuillez réessayer.');
}

header('Location: /Portfolio/toutpourunnouveaune/get_score?score=' . $score);
exit;
