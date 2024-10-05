<?php
session_start();

require '../../vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new \Database\DatabaseConnection();
    $db = $database->connect();
    $comment = new \Models\Comment($db);
    $commentManager = new \Controllers\CommentController($comment);

    $guide_id = $_POST['guide_id'];
    $contenu = $_POST['contenu'];
    $user_id = $_SESSION['user']['id'];

    $commentManager->addComment($guide_id, $user_id, $contenu);

    header('Location: /Portfolio/toutpourunnouveaune/guides?id=' . $guide_id);
    exit;
}