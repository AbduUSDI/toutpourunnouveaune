<?php

session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

require_once '../../../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

// Instanciation du modèle Forum
$forum = new \Models\Forum($db);

$threadController = new \Controllers\ForumController($forum);

$error_message = '';

// Traitement du formulaire si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nettoyage et validation des données du formulaire
    $title = htmlspecialchars(trim($_POST['title']));
    $body = htmlspecialchars(trim($_POST['body']));
    $user_id = $_SESSION['user']['id'];

    // Vérification que tous les champs sont remplis
    if (empty($title) || empty($body)) {
        $error_message = "Tous les champs sont requis.";
    } else {
        // Tentative de création de la nouvelle discussion
        if ($threadController->addThread($title, $body, $user_id)) {
            // Redirection vers la page principale du forum si succès
            header('Location: /Portfolio/toutpourunnouveaune/forum');
            exit;
        } else {
            // Message d'erreur si l'insertion échoue
            $error_message = "Erreur lors de la création de la discussion. Veuillez réessayer.";
        }
    }
}

require_once '../templates/header.php';
require_once '../templates/navbar_forum.php';
?>

<div class="container mt-5">
    <h1>Créer une nouvelle discussion</h1>
    
    <!-- Affichage du message d'erreur s'il existe -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire de création de discussion -->
    <form method="post" action="/Portfolio/toutpourunnouveaune/forum/threads/add">
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="body">Contenu</label>
            <textarea class="form-control" id="body" name="body" rows="5" required><?php echo isset($body) ? $body : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Créer la discussion</button>
    </form>
</div>

<?php
require_once '../templates/footer.php';
?>
