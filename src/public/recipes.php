<?php
session_start();
require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$recipe = new \Models\Recipe($db);
$recipeController = new \Controllers\RecipeController($recipe);
$recettes = $recipeController->getAllRecipes();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>

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
