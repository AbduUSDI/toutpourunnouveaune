<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/QuizModel.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

$quiz_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Vérification CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
    header('Location: manage_quizzes.php');
    exit;
}

if ($quiz_id) {
    $quiz->deleteQuiz($quiz_id);
}

header('Location: manage_quizzes.php');
exit;
