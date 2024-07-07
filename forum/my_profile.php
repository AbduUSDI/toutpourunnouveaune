<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/User.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$user = new User($db);
$currentUser = $user->getUserById($_SESSION['user']['id']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Optionnel : Validation des données
    // Exemple de validation simple pour email et username
    if (empty($username) || empty($email)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } else {
        // Optionnel : Mise à jour du mot de passe si un nouveau mot de passe est spécifié
        $newPassword = $_POST['new_password'];
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $user->updatePassword($_SESSION['user']['id'], $hashedPassword);
        }
        
        // Mettre à jour le profil de l'utilisateur
        if ($user->updateProfile($_SESSION['user']['id'], $username, $email, $password)) {
            // Mettre à jour la session avec les nouvelles informations si nécessaire
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['email'] = $email;
            
            // Redirection vers la même page pour éviter la soumission multiple du formulaire
            header('Location: my_profile.php');
            exit;
        } else {
            $error = "Erreur lors de la mise à jour du profil. Veuillez réessayer.";
        }
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
    <h1>Profil de <?php echo htmlspecialchars($currentUser['username']); ?></h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="my_profile.php" method="post">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="new_password">Nouveau mot de passe (optionnel)</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>
        <input type="submit" class="btn btn-primary" name="update_profile" value="Mettre à jour">
    </form>
</div>

<?php include_once 'templates/footer.php'; ?>
