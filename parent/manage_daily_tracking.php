<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/Tracking.php';

$database = new Database();
$db = $database->connect();

$dailyTracking = new Tracking($db);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $message = '';
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            switch ($action) {
                case 'create':
                    $utilisateur_id = filter_input(INPUT_POST, 'utilisateur_id', FILTER_SANITIZE_NUMBER_INT);
                    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT);
                    $heure_repas = filter_input(INPUT_POST, 'heure_repas', FILTER_SANITIZE_NUMBER_INT);
                    $duree_repas = filter_input(INPUT_POST, 'duree_repas', FILTER_SANITIZE_NUMBER_INT);
                    
                    if ($utilisateur_id && $date && $heure_repas && $duree_repas) {
                        $result = $dailyTracking->create($utilisateur_id, $date, $heure_repas, $duree_repas , $_SESSION['user']['id']);
                        $message = $result ? "Suivi quotidien créée avec succès." : "Erreur lors de la création du suivi quotidien.";
                    } else {
                        $message = "Tous les champs sont requis pour créer un suivi quotidien.";
                    }
                    break;

                case 'update':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    $utilisateur_id = filter_input(INPUT_POST, 'utilisateur_id', FILTER_SANITIZE_STRING);
                    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
                    $heure_repas = filter_input(INPUT_POST, 'heure_repas', FILTER_SANITIZE_STRING);
                    $duree_repas = filter_input(INPUT_POST, 'duree_repas', FILTER_SANITIZE_NUMBER_INT);

                    if ($id && $utilisateur_id && $date && $heure_repas && $duree_repas) {
                        $result = $dailyTracking->update($id, $utilisateur_id, $date, $heure_repas, $duree_repas);
                        $message = $result ? "Suivi quotidien mise à jour avec succès." : "Erreur lors de la mise à jour du suivi quotidien.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour un suivi quotidien.";
                    }
                    break;

                case 'delete':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $dailyTracking->delete($id);
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

$recettes = $dailyTracking->getTracking();

include '../templates/header.php';
include 'navbar_parent.php';
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
    <h1 class="mb-4">Gestion des Suivi quotidien</h1>

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
            Ajouter un suivi quotidien
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="mb-3">
                    <label for="utilisateur_id" class="form-label">Utilisateur</label>
                    <input type="text" class="form-control" id="utilisateur_id" name="utilisateur_id" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <textarea class="form-control" id="date" name="date" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="heure_repas" class="form-label">Heure tétée</label>
                    <textarea class="form-control" id="heure_repas" name="heure_repas" rows="3" required></textarea>
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
                        <?= htmlspecialchars($recette['utilisateur_id']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Date</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($recette['date'])) ?></p>
                        <h5 class="card-title">Heure tétée</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($recette['heure_repas'])) ?></p>
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
                                    <label for="utilisateur_id<?= $recette['id'] ?>" class="form-label">Utilisateur</label>
                                    <input type="text" class="form-control" id="utilisateur_id<?= $recette['id'] ?>" name="utilisateur_id" value="<?= htmlspecialchars($recette['utilisateur_id']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="date<?= $recette['id'] ?>" class="form-label">Date</label>
                                    <textarea class="form-control" id="date<?= $recette['id'] ?>" name="date" rows="3" required><?= htmlspecialchars($recette['date']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="heure_repas<?= $recette['id'] ?>" class="form-label">Heure tétée</label>
                                    <textarea class="form-control" id="heure_repas<?= $recette['id'] ?>" name="heure_repas" rows="3" required><?= htmlspecialchars($recette['heure_repas']) ?></textarea>
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
<?php include '../templates/footer.php'; ?>
