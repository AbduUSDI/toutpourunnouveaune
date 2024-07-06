<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/Comment.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$commentManager = new Comment($db);

$comment_id = $_GET['id'] ?? null;
$comment = null;

if ($comment_id) {
    $comment = $commentManager->getCommentById($comment_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    
    if ($commentManager->updateComment($comment_id, $content)) {
        $_SESSION['success_message'] = "Le commentaire a été mis à jour avec succès.";
        header('Location: manage_comment.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour du commentaire.";
    }
}

include '../templates/header.php';
include 'navbar_admin.php';
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
<div class="container mt-4">
    <h1>Modifier le commentaire</h1>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if ($comment): ?>
        <form action="edit_comment.php?id=<?php echo $comment_id; ?>" method="POST">
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

<?php include '../templates/footer.php'; ?>