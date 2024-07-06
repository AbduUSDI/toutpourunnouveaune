<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/AvisMedicaux.php';

$database = new Database();
$db = $database->connect();

$advice = new AvisMedicaux($db);

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

        // Stocker le message dans la session pour l'afficher après la redirection
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$advices = $advice->getAll();

include '../templates/header.php';
include 'navbar_doctor.php';
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
    <h1 class="mb-4">Gestion des Avis Médicaux</h1>

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
            Ajouter un nouvel avis médical
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
                <input type="hidden" name="medecin_id" value="<?php echo $_SESSION['user']['id']; ?>">
                <button type="submit" class="btn btn-info">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php foreach ($advices as $advice): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <?= htmlspecialchars($advice['titre']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Contenu</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($advice['contenu'])) ?></p>
                        <button class="btn btn-primary btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editForm<?= $advice['id'] ?>" aria-expanded="false" aria-controls="editForm<?= $advice['id'] ?>">
                            Modifier
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis médical ?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $advice['id'] ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <div class="collapse mt-3" id="editForm<?= $advice['id'] ?>">
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $advice['id'] ?>">
                                <div class="mb-3">
                                    <label for="titre<?= $advice['id'] ?>" class="form-label">Titre</label>
                                    <input type="text" class="form-control" id="titre<?= $advice['id'] ?>" name="titre" value="<?= htmlspecialchars($advice['titre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contenu<?= $advice['id'] ?>" class="form-label">Contenu</label>
                                    <textarea class="form-control" id="contenu<?= $advice['id'] ?>" name="contenu" rows="3" required><?= htmlspecialchars($advice['contenu']) ?></textarea>
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
