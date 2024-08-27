<?php
// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';

// Connexion à la base de données

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Utilisation d'une méthode pour afficher tous les utilisateurs

$users = $user->getAllUtilisateurs();

// Générer un jeton CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include_once '../../views/templates/header.php';
include_once '../../views/templates/navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1 class="my-4">Gérer les utilisateurs</h1>
    <div class="table-responsive">
        <a href="add_user.php" class="btn btn-info mb-4">Ajouter un utilisateur</a>
        <table class="table table-bordered table-striped table-hover" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Admin' : ($user['role_id'] == 2 ? 'Docteur' : 'Parent')); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="delete_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../../views/templates/footer.php'; ?>
