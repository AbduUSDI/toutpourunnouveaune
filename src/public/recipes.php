<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/RecipeModel.php';

$database = new Database();
$db = $database->connect();

$recipe = new Recipe($db);
$recettes = $recipe->getAll();

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
h1, .mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-4">
    <h1 class="mb-4">Nos recettes</h1>
    <div class="row">
        <?php if (empty($recettes)): ?>
            <p>Aucune recette disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($recettes as $recette): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info">
                            <h5 class="card-title"><?= htmlspecialchars($recette['titre']) ?></h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Ingrédients</h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($recette['ingredients'])) ?></p>
                            <h6 class="card-subtitle mb-2 text-muted">Instructions</h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($recette['instructions'])) ?></p>
                        </div>
                        <div class="card-footer text-muted">
                            Ajoutée le <?= date('d/m/Y', strtotime($recette['date_creation'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../views/templates/footer.php'; ?>
