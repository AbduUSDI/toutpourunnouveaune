<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/ForumModel.php';

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Instanciation du modèle Thread
$thread = new Thread($db);

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
        if ($thread->addThread($title, $body, $user_id)) {
            // Redirection vers la page principale du forum si succès
            header('Location: ../indexforum.php');
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

<style>
    h1, h2, h3 {
        text-align: center;
    }

    body {
        background-image: url('../../../assets/image/backgroundwebsite.jpg');
        padding-top: 48px; /* Un padding pour régler le décalage à cause de la classe fixed-top de la navbar */
    }

    h1, .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>

<div class="container mt-5">
    <h1>Créer une nouvelle discussion</h1>
    
    <!-- Affichage du message d'erreur s'il existe -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire de création de discussion -->
    <form method="post" action="add_thread.php">
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
