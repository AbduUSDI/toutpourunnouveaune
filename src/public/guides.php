<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/UserModel.php';
require_once '../models/GuideModel.php';
require_once '../models/CommentModel.php';

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Gestionnaires pour les guides et les commentaires
$guideManager = new Guide($db);
$commentManager = new Comment($db);

// Génération du token CSRF pour le formulaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupérer tous les guides ou un guide spécifique si un ID est fourni
$guides = isset($_GET['id']) ? [$guideManager->getGuideById($_GET['id'])] : $guideManager->getAllGuides();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center">Tous nos guides disponibles</h1>
    <hr>
    <br>
    <?php foreach ($guides as $guide): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($guide['titre']); ?></h2>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($guide['contenu'])); ?></p>
                <p class="text-muted">Publié par <?php echo htmlspecialchars($guide['auteur_nom']); ?> le <?php echo htmlspecialchars($guide['date_creation']); ?></p>
            </div>
        </div>

        <!-- Affichage des commentaires approuvés -->
        <br>
        <hr>
        <h3>Commentaires</h3>
        <hr>
        <br>
        <?php
        $comments = $commentManager->getApprovedCommentsByGuideId($guide['id']);
        if ($comments):
            foreach ($comments as $comment):
        ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                    <p class="text-muted">Commenté par <?php echo htmlspecialchars($comment['nom_utilisateur']); ?> le <?php echo htmlspecialchars($comment['date_creation']); ?></p>
                </div>
            </div>
        <?php
            endforeach;
        else:
            echo "<p>Soyez le premier à commenter ce guide.</p>";
        endif;
        ?>

        <!-- Formulaire pour ajouter un commentaire -->
        <?php if (isset($_SESSION['user'])): ?>
            <form action="add_comment.php" method="POST" class="mb-4">
                <input type="hidden" name="guide_id" value="<?php echo htmlspecialchars($guide['id']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="comment">Ajouter un commentaire</label>
                    <textarea class="form-control" id="comment" name="contenu" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-info">Soumettre le commentaire</button>
            </form>
        <?php else: ?>
            <p>Connectez-vous pour laisser un commentaire.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php include '../views/templates/footer.php'; ?>
