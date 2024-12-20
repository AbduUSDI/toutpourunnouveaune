<?php
require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseTPUNN())->connect();

$user = new \Models\UserOne($db);
$userController = new \Controllers\UserOneController($db, $user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = filter_input(INPUT_POST, 'nom_utilisateur', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'mot_de_passe', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role_id', FILTER_SANITIZE_NUMBER_INT);

    if ($user->getEmail($email)) {
        $error = "L'email est déjà utilisé.";
    } else {
        $result = $user->addUser($email, $password, $role, $username);
        if ($result) {
            $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
            header("Location: https://www.abduusdi.fr/toutpourunnouveaune/login?success=" . urlencode($success));
            exit();
        } else {
            $error = "Erreur lors de l'inscription. Veuillez réessayer.";
            header("Location: https://www.abduusdi.fr/toutpourunnouveaune/login?error=" . urlencode($error));
            exit();
        }
    }
}