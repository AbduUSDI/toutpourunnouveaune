<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/CommentModel.php';

$database = new Database();
$db = $database->connect();

$commentManager = new Comment($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: manage_comment.php');
        exit;
    }

    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    
    if (isset($_POST['approve']) && $comment_id) {
        if ($commentManager->approveComment($comment_id)) {
            $_SESSION['success_message'] = "Le commentaire a été approuvé avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'approbation du commentaire.";
        }
    } elseif (isset($_POST['delete']) && $comment_id) {
        if ($commentManager->deleteComment($comment_id)) {
            $_SESSION['success_message'] = "Le commentaire a été supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression du commentaire.";
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$pendingComments = $commentManager->getPendingComments();
$approvedComments = $commentManager->getApprovedComments();

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

    h1, .mt-4 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>

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
                    <form action="manage_comment.php" method="POST" class="d-inline">
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
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                    <p class="text-muted">Commenté par <?php echo htmlspecialchars($comment['nom_utilisateur']); ?> le <?php echo $comment['date_creation']; ?></p>
                    <form action="manage_comment.php" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                        <a href="edit_comment.php?id=<?php echo htmlspecialchars($comment['id']); ?>" class="btn btn-info">Modifier</a>
                        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../../views/templates/footer.php'; ?>
