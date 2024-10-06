<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 

$recipe = new \Models\Recipe($db);
$recipeController = new \Controllers\RecipeController($recipe);

// Protection CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($action) {
        $result = false;
        $message = '';

        try {
            switch ($action) {
                case 'create':
                    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
                    $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_STRING);
                    
                    if ($titre && $ingredients && $instructions) {
                        $result = $recipeController->createRecipe($titre, $ingredients, $instructions, $_SESSION['user']['id']);
                        $message = $result ? "Recette créée avec succès." : "Erreur lors de la création de la recette.";
                    } else {
                        $message = "Tous les champs sont requis pour créer une recette.";
                    }
                    break;

                case 'update':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
                    $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_STRING);
                    
                    if ($id && $titre && $ingredients && $instructions) {
                        $result = $recipeController->updateRecipe($id, $titre, $ingredients, $instructions);
                        $message = $result ? "Recette mise à jour avec succès." : "Erreur lors de la mise à jour de la recette.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour une recette.";
                    }
                    break;

                case 'delete':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $recipeController->deleteRecipe($id);
                        $message = $result ? "Recette supprimée avec succès." : "Erreur lors de la suppression de la recette.";
                    } else {
                        $message = "ID de recette invalide pour la suppression.";
                    }
                    break;

                default:
                    $message = "Action non reconnue.";
            }
        } catch (Exception $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
            error_log($e->getMessage());
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Générer un jeton CSRF pour cette session
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$recettes = $recipeController->getAllRecipes();

include '../../templates/header.php';
include '../../templates/navbar_admin.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Gestion des Recettes</h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . htmlspecialchars($_SESSION['message_type']) . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <div class="card mb-4">
        <div class="card-header text-center bg-info">
            Ajouter une nouvelle recette
        </div>
        <div class="card-body text-center">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" required>
                </div>
                <div class="mb-3">
                    <label for="ingredients" class="form-label">Ingrédients</label>
                    <textarea class="form-control" id="ingredients" name="ingredients" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-info">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php foreach ($recettes as $recette): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header text-center bg-info">
                        <?= htmlspecialchars($recette['titre']) ?>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Ingrédients</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars_decode($recette['ingredients'])) ?></p>
                        <h5 class="card-title">Instructions</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars_decode($recette['instructions'])) ?></p>
                        <button class="btn btn-warning btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?= htmlspecialchars($recette['id']) ?>" aria-expanded="false" aria-controls="editForm<?= htmlspecialchars($recette['id']) ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($recette['id']) ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <div class="collapse mt-3" id="editForm<?= htmlspecialchars($recette['id']) ?>">
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($recette['id']) ?>">
                                <div class="mb-3">
                                    <label for="titre<?= htmlspecialchars($recette['id']) ?>" class="form-label">Titre</label>
                                    <input type="text" class="form-control" id="titre<?= htmlspecialchars($recette['id']) ?>" name="titre" value="<?= htmlspecialchars_decode($recette['titre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ingredients<?= htmlspecialchars($recette['id']) ?>" class="form-label">Ingrédients</label>
                                    <textarea class="form-control" id="ingredients<?= htmlspecialchars($recette['id']) ?>" name="ingredients" rows="3" required><?= htmlspecialchars_decode($recette['ingredients']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="instructions<?= htmlspecialchars($recette['id']) ?>" class="form-label">Instructions</label>
                                    <textarea class="form-control" id="instructions<?= htmlspecialchars($recette['id']) ?>" name="instructions" rows="3" required><?= htmlspecialchars_decode($recette['instructions']) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modifierButtons = document.querySelectorAll('.btn-modifier');
    modifierButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var target = this.getAttribute('data-bs-target');
            var form = document.querySelector(target);
            if (form) {
                form.classList.toggle('show');
            }
        });
    });
});
</script>
<?php include '../../templates/footer.php'; ?>
