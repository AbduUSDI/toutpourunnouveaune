<?php
include_once '../functions/database.php';
include_once '../MongoDB.php';
include_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quizManager = new Quiz($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = $_POST['questions'];
    $quizId = $quizManager->createQuiz();

    foreach ($questions as $question) {
        $questionId = $quizManager->addQuestion($quizId, $question['text']);
        foreach ($question['options'] as $optionId => $optionText) {
            $isCorrect = ($question['correct'] == $optionId) ? 1 : 0;
            $quizManager->addOption($questionId, $optionText, $isCorrect);
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}