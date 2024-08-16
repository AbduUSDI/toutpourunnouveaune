<?php
require_once '../../config/Database.php';
require_once '../models/UserModel.php';

$database = new Database();
$db = $database->connect();
$user = new User2($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $userData = $user->getUserByResetToken($token);

    if (!$userData) {
        die("Token invalide ou expiré.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $userData = $user->getUserByResetToken($token);

    if ($userData) {
        if ($user->updatePassword($userData['id'], $newPassword)) {
            echo "Votre mot de passe a été réinitialisé avec succès.";
            // Rediriger vers la page de connexion
            header('Location: login.php');
            exit;
        } else {
            echo "Une erreur s'est produite lors de la réinitialisation du mot de passe.";
        }
    } else {
        echo "Token invalide ou expiré.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <h1>Réinitialisation du mot de passe</h1>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">Nouveau mot de passe :</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">Changer le mot de passe</button>
    </form>
</body>
</html>