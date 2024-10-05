<?php
session_start();

require '../../vendor/autoload.php';

// Connexion à la base de données
$database = new \Database\DatabaseConnection();
$db = $database->connect();

// Initialisation des Models pour les inclure dans les contrôleurs
$guide = new \Models\Guide($db);
$comment = new \Models\Comment($db);

// Gestionnaires pour les guides et les commentaires
$guideController = new \Controllers\GuideController($guide);
$commentController = new \Controllers\CommentController($comment);

// Génération du token CSRF pour le formulaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupérer tous les guides ou un guide spécifique si un ID est fourni
$guides = isset($_GET['id']) ? [$guideController->getGuideById($_GET['id'])] : $guideController->getAllGuides();

include '../views/templates/header.php';
include '../views/templates/navbar.php';

?>


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
        $comments = $commentController->getApprovedCommentsByGuideId($guide['id']);
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
            <form action="/Portfolio/toutpourunnouveaune/add_comment" method="POST" class="mb-4">
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
