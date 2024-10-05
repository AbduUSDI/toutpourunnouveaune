<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header('Location: manage_users.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$userManager = new User($db);
$user = $userManager->getUtilisateurParId($user_id);

if (!$user) {
    header('Location: manage_users.php');
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

    header('Location: manage_users.php');
    exit;
}

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
    <h1 class="my-4">Modifier Utilisateur</h1>
    <form action="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" method="POST">
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
<?php include '../../views/templates/footer.php'; ?>
