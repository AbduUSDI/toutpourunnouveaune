<?php
// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 

// Vérifier si l'ID de l'utilisateur à supprimer est présent dans la requête
$userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$userId) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit();
}

// Vérifier le jeton CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit();
}

// Instance User ici pour utiliser toutes le méthodes en rapport avec les utilisateurs
$user = new \Models\User($db);
$userController = new \Controllers\UserController($db, $user);

try {
    // Supprimer l'utilisateur de la base de données
    $userController->deleteUser($userId);

    // Rediriger vers la page des utilisateurs avec un message de succès
    $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit();
} catch (PDOException $e) {
    // Rediriger vers la page des utilisateurs avec un message d'erreur
    $_SESSION['error_message'] = "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit();
}
