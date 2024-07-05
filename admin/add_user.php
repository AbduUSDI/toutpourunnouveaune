<?php
// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon redirection vers login.php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/User.php';

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Instance User pour utiliser les méthodes en rapport aux utilisateurs
$user = new User($db);

// Traitement et récupération des données du formulaire (POST) d'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; // Le hachage sera fait dans la méthode addUser
    $role_id = $_POST['role_id'];
    $username = $_POST['username'];

    // Utilisation de la méthode préparée "addUser" pour ajouter le nouvel utilisateur
    $user->addUser($email, $password, $role_id, $username);

    // Redirection vers la page de gestion des utilisateurs
    header('Location: manage_users.php');
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>

<!-- Conteneur pour afficher le formulaire (POST) pour ajouter un utilisateur -->
<div class="container">
    <h1 class="my-4">Ajouter un Utilisateur</h1>
    <form action="add_user.php" method="POST">
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

<?php include '../templates/footer.php'; ?>