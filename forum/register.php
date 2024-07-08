<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/User.php';

if (isset($_SESSION['user'])) {
    header('Location: indexforum.php');
    exit;
}

$database = new Database2();
$db = $database->connect();
$user = new User2($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    if ($user->register($username, $email, $password, $role)) {
        $newUser = $user->login($email, $password);
        $_SESSION['user'] = $newUser;
        header('Location: indexforum.php');
        exit;
    } else {
        $error = "Erreur lors de l'inscription. Veuillez réessayer.";
    }
}

include_once 'templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1 class="my-4">Inscription</h1>
    <form action="register.php" method="post">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="input-group-append">
            <input type="password" class="form-control" id="password" name="password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select class="form-control" id="role" name="role" required>
                <option value="parent">Parent</option>
                <option value="docteur">Docteur</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    <p class="mt-3">Déjà inscrit ? <a class="btn btn-outline-info" href="login.php">Connectez-vous ici</a></p>
</div>
<script>
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

<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<?php include_once 'templates/footer.php'; ?>
