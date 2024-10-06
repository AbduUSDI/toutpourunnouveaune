<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect();

$comment = new \Models\Comment($db);
$commentController = new \Controllers\CommentController($comment);

$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$comment = null;

if ($comment_id) {
    $comment = $commentController->getCommentById($comment_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: /Portfolio/toutpourunnouveaune/admin/comments/edit/' . $comment_id);
        exit;
    }
    
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

    if ($commentController->updateComment($comment_id, $content)) {
        $_SESSION['success_message'] = "Le commentaire a été mis à jour avec succès.";
        header('Location: /Portfolio/toutpourunnouveaune/admin/comments');
        exit;
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour du commentaire.";
    }
}

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../templates/header.php';
include '../../templates/navbar_admin.php';
?>

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
        <form action="/Portfolio/toutpourunnouveaune/admin/comments/edit/<?php echo htmlspecialchars($comment_id); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="content">Contenu du commentaire</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars_decode($comment['contenu']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-info">Mettre à jour</button>
            <a href="/Portfolio/toutpourunnouveaune/admin/comments" class="btn btn-secondary">Annuler</a>
        </form>
    <?php else: ?>
        <p>Le commentaire demandé n'existe pas.</p>
        <a href="/Portfolio/toutpourunnouveaune/admin/comments" class="btn btn-secondary">Retour à la liste des commentaires</a>
    <?php endif; ?>
</div>

<?php include '../../templates/footer.php'; ?>
