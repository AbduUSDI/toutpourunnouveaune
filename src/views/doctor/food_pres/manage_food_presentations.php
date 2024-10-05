<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/FoodPresentationModel.php';

$database = new Database();
$db = $database->connect();

$recipe = new FoodPresentation($db);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $message = '';
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            switch ($action) {
                case 'create':
                    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
                    $groupe_age = filter_input(INPUT_POST, 'groupe_age', FILTER_SANITIZE_STRING);
                    
                    if ($titre && $contenu && $groupe_age) {
                        $result = $recipe->create($titre, $contenu, $groupe_age, $_SESSION['user']['id']);
                        $message = $result ? "Présentation alimentaire créée avec succès." : "Erreur lors de la création de la présentation alimentaire.";
                    } else {
                        $message = "Tous les champs sont requis pour créer une présentation alimentaire.";
                    }
                    break;

                case 'update':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
                    $groupe_age = filter_input(INPUT_POST, 'groupe_age', FILTER_SANITIZE_STRING);
                    
                    if ($id && $titre && $contenu && $groupe_age) {
                        $result = $recipe->update($id, $titre, $contenu, $groupe_age);
                        $message = $result ? "Présentation alimentaire mise à jour avec succès." : "Erreur lors de la mise à jour de la présentation alimentaire.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour une présentation alimentaire.";
                    }
                    break;

                case 'delete':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $recipe->delete($id);
                        $message = $result ? "Présentation alimentaire supprimée avec succès." : "Erreur lors de la suppression de la présentation alimentaire.";
                    } else {
                        $message = "ID de présentation alimentaire invalide pour la suppression.";
                    }
                    break;

                default:
                    $message = "Action non reconnue.";
            }
        } catch (Exception $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
            error_log($e->getMessage());
        }

        // Stocker le message dans la session pour l'afficher après la redirection
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$recettes = $recipe->getAll();

include '../../views/templates/header.php';
include '../../views/templates/navbar_doctor.php';
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
    padding-top: 48px;
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-4">
    <h1 class="mb-4">Gestion des Présentations Alimentaires</h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <div class="card mb-4">
        <div class="card-header">
            Ajouter une nouvelle présentation alimentaire
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" required>
                </div>
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" id="contenu" name="contenu" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="groupe_age" class="form-label">Tranche d'âge</label>
                    <textarea class="form-control" id="groupe_age" name="groupe_age" rows="3" required></textarea>
                </div>
                <input type="hidden" name="medecin_id" value="<?php echo $_SESSION['user']['id']; ?>">
                <button type="submit" class="btn btn-info">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php foreach ($recettes as $recette): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <?= htmlspecialchars($recette['titre']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Contenu</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($recette['contenu'])) ?></p>
                        <h5 class="card-title">Tranche d'âge</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($recette['groupe_age'])) ?></p>
                        <button class="btn btn-primary btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?= $recette['id'] ?>" aria-expanded="false" aria-controls="editForm<?= $recette['id'] ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette présentation alimentaire ?');">
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
                                    <input type="text" class="form-control" id="titre<?= $recette['id'] ?>" name="titre" value="<?= htmlspecialchars($recette['titre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contenu<?= $recette['id'] ?>" class="form-label">Contenu</label>
                                    <textarea class="form-control" id="contenu<?= $recette['id'] ?>" name="contenu" rows="3" required><?= htmlspecialchars($recette['contenu']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="groupe_age<?= $recette['id'] ?>" class="form-label">Tranche d'âge</label>
                                    <textarea class="form-control" id="groupe_age<?= $recette['id'] ?>" name="groupe_age" rows="3" required><?= htmlspecialchars($recette['groupe_age']) ?></textarea>
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
<?php include '../../views/templates/footer.php'; ?>
