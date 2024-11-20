<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: https://www.abduusdi.fr/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseTPUNN())->connect(); 

$quiz = new \Models\QuizTPUNN($db);
$quizController = new \Controllers\QuizTPUNNController($quiz);

$quiz_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Vérification CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
    header('Location: https://www.abduusdi.fr/toutpourunnouveaune/admin/quiz');
    exit;
}

if ($quiz_id) {
    $quizController->deleteQuiz($quiz_id);
}

header('Location: https://www.abduusdi.fr/toutpourunnouveaune/admin/quiz');
exit;
