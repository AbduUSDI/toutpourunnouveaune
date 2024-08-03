<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/CommentModel.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();
    $commentManager = new Comment($db);

    $guide_id = $_POST['guide_id'];
    $contenu = $_POST['contenu'];
    $user_id = $_SESSION['user']['id'];

    $commentManager->addComment($guide_id, $user_id, $contenu);

    header('Location: guides.php?id=' . $guide_id);
    exit;
}