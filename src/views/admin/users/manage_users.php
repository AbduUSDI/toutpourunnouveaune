<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 
// Connexion à la base de données

$user = new \Models\User($db);
$userController = new \Controllers\UserController($db, $user);

// Utilisation d'une méthode pour afficher tous les utilisateurs
$users = $userController->getAllUtilisateurs();

// Générer un jeton CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include_once '../../templates/header.php';
include_once '../../templates/navbar_admin.php';
?>

<div class="container mt-5">
    <h1 class="my-4">Gérer les utilisateurs</h1>
    <div class="table-responsive">
        <a href="/Portfolio/toutpourunnouveaune/admin/users/add" class="btn btn-info mb-4">Ajouter un utilisateur</a>
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
                            <a href="/Portfolio/toutpourunnouveaune/admin/users/edit/<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="/Portfolio/toutpourunnouveaune/admin/users/delete" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
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

<?php include_once '../../templates/footer.php'; ?>
