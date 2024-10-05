<?php
require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();


$user = new \Models\UserTwo($db);
$userController = new \Controllers\UserTwoController($user);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
    $userData = $user->getUserByResetToken($token);

    if (!$userData) {
        die("Token invalide ou expiré.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Action non autorisée.');
    }

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $userData = $user->getUserByResetToken($token);

    if ($userData) {
        if ($user->updatePassword($userData['id'], $newPassword)) {
            echo "Votre mot de passe a été réinitialisé avec succès.";
            header('Location: /Portfolio/toutpourunnouveaune/login');
            exit;
        } else {
            echo "Une erreur s'est produite lors de la réinitialisation du mot de passe.";
        }
    } else {
        echo "Token invalide ou expiré.";
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
include_once '../views/templates/header.php'
?>
    <div class="container mt-5">
        <h1>Réinitialisation du mot de passe</h1>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit">Changer le mot de passe</button>
        </form>
    </div>
</body>
</html>
