<?php
include_once '../functions/database.php';
include_once '../MongoDB.php';
include_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quizManager = new Quiz($db);

if (isset($_GET['id'])) {
    $quizId = $_GET['id'];
    $quizManager->deleteQuiz($quizId);
    header("Location: manage_quizzes.php");
    exit();
} else {
    die("ID de quiz non spécifié.");
}