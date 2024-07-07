<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/User.php';

if (isset($_SESSION['user'])) {
    header('Location: indexforum.php');
    exit;
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $loggedInUser = $user->login($email, $password);
    if ($loggedInUser) {
        $_SESSION['user'] = $loggedInUser;
        header('Location: indexforum.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}

include_once 'templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
  body {
    background-image: url('../image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
  }
  h1,h2,h3 {
    text-align: center;
}
</style>


<div class="container">
    <h1 class="my-4">Connexion</h1>
    <form action="login.php" method="post">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            
        
        <div class="input-group-append">
            <input type="password" class="form-control" id="password" name="password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
        </div>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <p class="mt-3">Pas encore inscrit ? <a href="register.php">Inscrivez-vous ici</a>.</p>
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
