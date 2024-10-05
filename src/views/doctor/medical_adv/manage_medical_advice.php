<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/AvisMedicauxModel.php';

$database = new Database();
$db = $database->connect();

$advice = new AvisMedicaux($db);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = false;
    $message = '';
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    try {
        switch ($action) {
            case 'create':
                $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
                
                if ($titre && $contenu) {
                    $result = $advice->create($titre, $contenu, $_SESSION['user']['id']);
                    $message = $result ? "Avis médical créé avec succès." : "Erreur lors de la création de l'avis médical.";
                } else {
                    $message = "Tous les champs sont requis pour créer un avis médical.";
                }
                break;

            case 'update':
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
                $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
                
                if ($id && $titre && $contenu) {
                    $result = $advice->update($id, $titre, $contenu);
                    $message = $result ? "Avis médical mis à jour avec succès." : "Erreur lors de la mise à jour de l'avis médical.";
                } else {
                    $message = "Tous les champs sont requis pour mettre à jour un avis médical.";
                }
                break;

            case 'delete':
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                
                if ($id) {
                    $result = $advice->delete($id);
                    $message = $result ? "Avis médical supprimé avec succès." : "Erreur lors de la suppression de l'avis médical.";
                } else {
                    $message = "ID d'avis médical invalide pour la suppression.";
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

$advices = $advice->getAll();

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
    <h1 class="mb-4">Gestion des Avis Médicaux</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Ajouter un nouvel avis médical</div>
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
                <button type="submit" class="btn btn-info">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php foreach ($advices as $advice): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header"><?php echo htmlspecialchars($advice['titre']); ?></div>
                    <div class="card-body">
                        <h5 class="card-title">Contenu</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($advice['contenu'])); ?></p>
                        <button class="btn btn-primary btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?php echo $advice['id']; ?>" aria-expanded="false" aria-controls="editForm<?php echo $advice['id']; ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis médical ?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $advice['id']; ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <div class="collapse mt-3" id="editForm<?php echo $advice['id']; ?>">
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?php echo $advice['id']; ?>">
                                <div class="mb-3">
                                    <label for="titre<?php echo $advice['id']; ?>" class="form-label">Titre</label>
                                    <input type="text" class="form-control" id="titre<?php echo $advice['id']; ?>" name="titre" value="<?php echo htmlspecialchars($advice['titre']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contenu<?php echo $advice['id']; ?>" class="form-label">Contenu</label>
                                    <textarea class="form-control" id="contenu<?php echo $advice['id']; ?>" name="contenu" rows="3" required><?php echo htmlspecialchars($advice['contenu']); ?></textarea>
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
