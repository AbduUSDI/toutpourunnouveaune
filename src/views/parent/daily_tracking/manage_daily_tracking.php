<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}


require_once '../../../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$tracking = new \Models\Tracking($db);
$dailyTracking = new \Controllers\TrackingController($tracking);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = false;
    $message = '';
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    try {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        switch ($action) {
            case 'create':
            case 'update':
                $utilisateur_id = filter_input(INPUT_POST, 'utilisateur_id', FILTER_SANITIZE_NUMBER_INT);
                $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
                $heure_repas = filter_input(INPUT_POST, 'heure_repas', FILTER_SANITIZE_STRING);
                $duree_repas = filter_input(INPUT_POST, 'duree_repas', FILTER_SANITIZE_NUMBER_INT);
                $heure_change = filter_input(INPUT_POST, 'heure_change', FILTER_SANITIZE_STRING);
                $medicament = filter_input(INPUT_POST, 'medicament', FILTER_SANITIZE_STRING);
                $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

                // Validation des champs pour 'create' et 'update'
                if (!$utilisateur_id || !$date || !$heure_repas) {
                    $message = "Les champs utilisateur, date et heure de repas sont requis.";
                } else {
                    if ($action === 'create') {
                        $result = $dailyTracking->createTracking($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes);
                        $message = $result ? "Suivi quotidien créé avec succès." : "Erreur lors de la création du suivi quotidien.";
                    } elseif ($action === 'update') {
                        $result = $dailyTracking->updateTracking($id, $utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes);
                        $message = $result ? "Suivi quotidien mis à jour avec succès." : "Erreur lors de la mise à jour du suivi quotidien.";
                    }
                }
                break;

            case 'delete':
                // Seule la validation de l'ID est nécessaire pour la suppression
                if (!$id) {
                    $message = "ID invalide pour la suppression.";
                } else {
                    $result = $dailyTracking->deleteTracking($id);
                    $message = $result ? "Suivi quotidien supprimé avec succès." : "Erreur lors de la suppression du suivi quotidien.";
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

$recettes = $dailyTracking->getAllTracking();

include '../../templates/header.php';
include '../../templates/navbar_parent.php';
?>
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
                    <input type="number" class="form-control" id="utilisateur_id" name="utilisateur_id" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="mb-3">
                    <label for="heure_repas" class="form-label">Heure de repas</label>
                    <input type="time" class="form-control" id="heure_repas" name="heure_repas" required>
                </div>
                <div class="mb-3">
                    <label for="duree_repas" class="form-label">Durée du repas (minutes)</label>
                    <input type="number" class="form-control" id="duree_repas" name="duree_repas">
                </div>
                <div class="mb-3">
                    <label for="heure_change" class="form-label">Heure de changement</label>
                    <input type="time" class="form-control" id="heure_change" name="heure_change">
                </div>
                <div class="mb-3">
                    <label for="medicament" class="form-label">Médicament</label>
                    <input type="text" class="form-control" id="medicament" name="medicament">
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes"></textarea>
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
                        <?= htmlspecialchars($recette['utilisateur_id']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Date</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['date']) ?></p>
                        <h5 class="card-title">Heure de repas</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['heure_repas']) ?></p>
                        <h5 class="card-title">Durée du repas</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['duree_repas']) ?></p>
                        <h5 class="card-title">Heure de changement</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['heure_change']) ?></p>
                        <h5 class="card-title">Médicament</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['medicament']) ?></p>
                        <h5 class="card-title">Notes</h5>
                        <p class="card-text"><?= htmlspecialchars($recette['notes']) ?></p>
                        <button class="btn btn-primary btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?= $recette['id'] ?>" aria-expanded="false" aria-controls="editForm<?= $recette['id'] ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline-block">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $recette['id'] ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                    <div class="collapse" id="editForm<?= $recette['id'] ?>">
                        <div class="card card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $recette['id'] ?>">
                                <div class="mb-3">
                                    <label for="utilisateur_id_<?= $recette['id'] ?>" class="form-label">Utilisateur</label>
                                    <input type="number" class="form-control" id="utilisateur_id_<?= $recette['id'] ?>" name="utilisateur_id" value="<?= htmlspecialchars($recette['utilisateur_id']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="date_<?= $recette['id'] ?>" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date_<?= $recette['id'] ?>" name="date" value="<?= htmlspecialchars($recette['date']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="heure_repas_<?= $recette['id'] ?>" class="form-label">Heure de repas</label>
                                    <input type="time" class="form-control" id="heure_repas_<?= $recette['id'] ?>" name="heure_repas" value="<?= htmlspecialchars($recette['heure_repas']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="duree_repas_<?= $recette['id'] ?>" class="form-label">Durée du repas (minutes)</label>
                                    <input type="number" class="form-control" id="duree_repas_<?= $recette['id'] ?>" name="duree_repas" value="<?= htmlspecialchars($recette['duree_repas']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="heure_change_<?= $recette['id'] ?>" class="form-label">Heure de changement</label>
                                    <input type="time" class="form-control" id="heure_change_<?= $recette['id'] ?>" name="heure_change" value="<?= htmlspecialchars($recette['heure_change']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="medicament_<?= $recette['id'] ?>" class="form-label">Médicament</label>
                                    <input type="text" class="form-control" id="medicament_<?= $recette['id'] ?>" name="medicament" value="<?= htmlspecialchars($recette['medicament']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="notes_<?= $recette['id'] ?>" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes_<?= $recette['id'] ?>" name="notes"><?= htmlspecialchars($recette['notes']) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-info">Mettre à jour</button>
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
