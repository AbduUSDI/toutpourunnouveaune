<?php
session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {     
    header('Location: /Portfolio/toutpourunnouveaune/login');     
    exit; 
}

require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 

$recipeModel = new \Models\Recipe($db);
$recipe = new \Controllers\RecipeController($recipeModel);

// Essai d'une méthode pour afficher et planifier les messages
function setSessionMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function displaySessionMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
            $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
            $instructions = filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_STRING);

            switch ($action) {
                case 'create':
                    if ($titre && $ingredients && $instructions) {
                        $result = $recipe->createRecipe($titre, $ingredients, $instructions, $_SESSION['user']['id']);
                        setSessionMessage("Recette créée avec succès.");
                    } else {
                        setSessionMessage("Tous les champs sont requis pour créer une recette.", 'danger');
                    }
                    break;

                case 'update':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    if ($id && $titre && $ingredients && $instructions) {
                        $result = $recipe->updateRecipe($id, $titre, $ingredients, $instructions);
                        setSessionMessage("Recette mise à jour avec succès.");
                    } else {
                        setSessionMessage("Tous les champs sont requis pour mettre à jour une recette.", 'danger');
                    }
                    break;

                case 'delete':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    if ($id) {
                        $result = $recipe->deleteRecipe($id);
                        setSessionMessage("Recette supprimée avec succès.");
                    } else {
                        setSessionMessage("ID de recette invalide pour la suppression.", 'danger');
                    }
                    break;

                default:
                    setSessionMessage("Action non reconnue.", 'danger');
            }
        } catch (Exception $e) {
            setSessionMessage("Une erreur est survenue : " . $e->getMessage(), 'danger');
            error_log($e->getMessage());
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$recettes = $recipe->getAllRecipes();

include '../../templates/header.php';
include '../../templates/navbar_doctor.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Gestion des Recettes</h1>

    <?php displaySessionMessage(); ?>

    <div class="card mb-4">
        <div class="card-header">
            Ajouter une nouvelle recette
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="create">
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
                    <div class="card-header">
                        <?= htmlspecialchars_decode($recette['titre']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Ingrédients</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars_decode($recette['ingredients'])) ?></p>
                        <h5 class="card-title">Instructions</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars_decode($recette['instructions'])) ?></p>
                        <button class="btn btn-primary btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?= $recette['id'] ?>" aria-expanded="false" aria-controls="editForm<?= $recette['id'] ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $recette['id'] ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <div class="collapse mt-3" id="editForm<?= $recette['id'] ?>">
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $recette['id'] ?>">
                                <div class="mb-3">
                                    <label for="titre<?= $recette['id'] ?>" class="form-label">Titre</label>
                                    <input type="text" class="form-control" id="titre<?= $recette['id'] ?>" name="titre" value="<?= htmlspecialchars_decode($recette['titre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ingredients<?= $recette['id'] ?>" class="form-label">Ingrédients</label>
                                    <textarea class="form-control" id="ingredients<?= $recette['id'] ?>" name="ingredients" rows="3" required><?= htmlspecialchars_decode($recette['ingredients']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="instructions<?= $recette['id'] ?>" class="form-label">Instructions</label>
                                    <textarea class="form-control" id="instructions<?= $recette['id'] ?>" name="instructions" rows="3" required><?= htmlspecialchars_decode($recette['instructions']) ?></textarea>
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
