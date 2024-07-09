<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/Quiz.php';
require_once 'MongoDB.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);
$mongoClient = new MongoDB();
$collection = $mongoClient->getCollection('scores');

$quiz_id = $_POST['quiz_id'];
$user_answers = $_POST['answers'];

$score = $quiz->calculateScore($quiz_id, $user_answers);

$user_id = $_SESSION['user']['id'];

// Cherchez si l'utilisateur existe déjà dans la collection
$existingUser = $collection->findOne(['user_id' => $user_id]);

if ($existingUser) {
    // Mettez à jour le score total et ajoutez le score du quiz à la liste des scores des quiz
    $newTotalScore = $score + $existingUser['total_score'];
    
    $quizScores = $existingUser['quiz_scores'];
    $quizFound = false;
    
    foreach ($quizScores as &$quizScore) {
        if ($quizScore['quiz_id'] == $quiz_id) {
            $quizScore['score'] += $score;
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
    // Créez un nouveau document pour l'utilisateur
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

header('Location: get_score.php?score=' . $score);
exit;
