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
$userManager = new \Controllers\UserController($db, $user);

if (!isset($_GET['id'])) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit;
}

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit;
}

$user = $userManager->getUtilisateurParId($user_id);

if (!$user) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $user_id);
        exit;
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if ($username && $email && $role_id) {
        $userManager->updateUser($user_id, $email, $role_id, $username, $password);
        $_SESSION['message'] = "Utilisateur mis à jour avec succès.";
    } else {
        $_SESSION['error_message'] = "Tous les champs sont requis.";
    }

    header('Location: /Portfolio/toutpourunnouveaune/admin/users');
    exit;
}

// Générer un jeton CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include_once '../../templates/header.php';
include_once '../../templates/navbar_admin.php';
?>

<div class="container mt-5">
    <h1 class="my-4">Modifier Utilisateur</h1>
    <form action="/Portfolio/toutpourunnouveaune/admin/users/edit/<?php echo htmlspecialchars($user['id']); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['nom_utilisateur']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="role_id">Rôle</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="1" <?php if ($user['role_id'] == 1) echo 'selected'; ?>>Administrateur</option>
                <option value="2" <?php if ($user['role_id'] == 2) echo 'selected'; ?>>Docteur</option>
                <option value="3" <?php if ($user['role_id'] == 3) echo 'selected'; ?>>Parent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
<script>
// Script pour afficher/masquer le mot de passe
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const eyeIcon = this.querySelector('i');
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});
</script>
<?php include '../../templates/footer.php'; ?>
