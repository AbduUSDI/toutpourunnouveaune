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
$collection->insertOne([
    'user_id' => $user_id,
    'quiz_id' => $quiz_id,
    'score' => $score,
]);

header('Location: get_score.php?score=' . $score);
exit;
