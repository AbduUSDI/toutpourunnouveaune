<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

$quiz_id = $_GET['id'];
$quiz->deleteQuiz($quiz_id);

header('Location: manage_quizzes.php');
exit;