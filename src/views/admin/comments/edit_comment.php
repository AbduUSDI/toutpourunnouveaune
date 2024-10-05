<?php
session_start();
require_once '../../vendor/autoload.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}


$db = (new Database\DatabaseConnection())->connect();

$comment = new \Models\Comment($db);
$commentManager = new \Controllers\CommentController($comment);

$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$comment = null;

if ($comment_id) {
    $comment = $commentManager->getCommentById($comment_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: edit_comment.php?id=' . $comment_id);
        exit;
    }
    
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

    if ($commentManager->updateComment($comment_id, $content)) {
        $_SESSION['success_message'] = "Le commentaire a été mis à jour avec succès.";
        header('Location: manage_comment.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour du commentaire.";
    }
}

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../views/templates/header.php';
include '../../views/templates/navbar_admin.php';
?>

<style>
    h1, h2, h3 {
        text-align: center;
    }

    body {
        background-image: url('../../../assets/image/background.jpg');
        padding-top: 48px;
    }

    h1, .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>

<div class="container mt-5">
    <h1>Modifier le commentaire</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if ($comment): ?>
        <form action="edit_comment.php?id=<?php echo htmlspecialchars($comment_id); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="content">Contenu du commentaire</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($comment['contenu']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-info">Mettre à jour</button>
            <a href="manage_comment.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php else: ?>
        <p>Le commentaire demandé n'existe pas.</p>
        <a href="manage_comment.php" class="btn btn-secondary">Retour à la liste des commentaires</a>
    <?php endif; ?>
</div>

<?php include '../../views/templates/footer.php'; ?>
