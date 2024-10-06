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

$user = new \Models\User($db);
$userController = new \Controllers\UserController($db, $user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; // Le hachage sera fait dans la méthode addUser
    $role_id = $_POST['role_id'];
    $username = $_POST['username'];

    // Utilisation de la méthode préparée "addUser" pour ajouter le nouvel utilisateur
    $userController->addUser($email, $password, $role_id, $username);

    // Redirection vers la page de gestion des utilisateurs
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit;
}

include_once '../../templates/header.php';
include_once '../../templates/navbar_admin.php';
?>
<!-- Formulaire d'ajout utilisateur, utilisation de la méthode POST -->
<div class="container mt-5">
    <h1 class="my-4">Ajouter un Utilisateur</h1>
    <form action="/Portfolio/toutpourunnouveaune/admin/users/add" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role_id">Rôle</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="1">Administrateur</option>
                <option value="2">Médecin</option>
                <option value="3">Parent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include_once '../../templates/footer.php'; ?>