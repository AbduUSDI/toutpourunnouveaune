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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: /Portfolio/toutpourunnouveaune/admin/comments');
        exit;
    }

    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    
    if (isset($_POST['approve']) && $comment_id) {
        if ($commentController->approveComment($comment_id)) {
            $_SESSION['success_message'] = "Le commentaire a été approuvé avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'approbation du commentaire.";
        }
    } elseif (isset($_POST['delete']) && $comment_id) {
        if ($commentController->deleteComment($comment_id)) {
            $_SESSION['success_message'] = "Le commentaire a été supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression du commentaire.";
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$pendingComments = $commentController->getPendingComments();
$approvedComments = $commentController->getApprovedComments();

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../templates/header.php';
include '../../templates/navbar_admin.php';
?>
<div class="container mt-4">
    <hr>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']);
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <h1>Approuver les commentaires</h1>
    <?php if (empty($pendingComments)): ?>
        <p>Aucun commentaire en attente d'approbation.</p>
    <?php else: ?>
        <?php foreach ($pendingComments as $comment): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                    <p class="text-muted">Par <?php echo htmlspecialchars($comment['nom_utilisateur']); ?> le <?php echo $comment['date_creation']; ?></p>
                    <form action="/Portfolio/toutpourunnouveaune/admin/comments" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                        <button type="submit" name="approve" class="btn btn-success">Approuver</button>
                        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h1>Commentaires approuvés</h1>
    <?php if (empty($approvedComments)): ?>
        <p>Aucun commentaire approuvé.</p>
    <?php else: ?>
        <?php foreach ($approvedComments as $comment): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars_decode($comment['contenu'])); ?></p>
                    <p class="text-muted">Commenté par <?php echo htmlspecialchars_decode($comment['nom_utilisateur']); ?> le <?php echo $comment['date_creation']; ?></p>
                    <form action="/Portfolio/toutpourunnouveaune/admin/comments" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                        <a href="/Portfolio/toutpourunnouveaune/admin/comments/edit/<?php echo htmlspecialchars($comment['id']); ?>" class="btn btn-info">Modifier</a>
                        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../../templates/footer.php'; ?>
