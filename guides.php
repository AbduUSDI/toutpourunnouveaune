<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/User.php';
require_once 'functions/Guide.php';
require_once 'functions/Comment.php';

$database = new Database();
$db = $database->connect();

$guideManager = new Guide($db);
$commentManager = new Comment($db);

// Récupérer tous les guides ou un guide spécifique si un ID est fourni
$guides = isset($_GET['id']) ? [$guideManager->getGuideById($_GET['id'])] : $guideManager->getAllGuides();

include 'templates/header.php';
include 'templates/navbar.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

</style>
<div class="container mt-4">
    <?php foreach ($guides as $guide): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($guide['titre']); ?></h2>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($guide['contenu'])); ?></p>
                <p class="text-muted">Publié par <?php echo htmlspecialchars($guide['auteur_nom']); ?> le <?php echo $guide['date_creation']; ?></p>
            </div>
        </div>

        <!-- Affichage des commentaires approuvés -->
        <h3>Commentaires</h3>
        <?php
        $comments = $commentManager->getApprovedCommentsByGuideId($guide['id']);
        foreach ($comments as $comment):
        ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                    <p class="text-muted">Commenté par <?php echo htmlspecialchars($comment['nom_utilisateur']); ?> le <?php echo $comment['date_creation']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Formulaire pour ajouter un commentaire -->
        <?php if (isset($_SESSION['user'])): ?>
            <form action="add_comment.php" method="POST" class="mb-4">
                <input type="hidden" name="guide_id" value="<?php echo $guide['id']; ?>">
                <div class="form-group">
                    <label for="comment">Ajouter un commentaire</label>
                    <textarea class="form-control" id="comment" name="contenu" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Soumettre le commentaire</button>
            </form>
        <?php else: ?>
            <p>Connectez-vous pour laisser un commentaire.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php include 'templates/footer.php'; ?>