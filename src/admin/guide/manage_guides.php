<?php
session_start();
// Vérifier si l'utilisateur est connecté et a les droits d'accès
if (!isset($_SESSION['user']) || ($_SESSION['user']['role_id'] != 1 && $_SESSION['user']['role_id'] != 2)) {
    header('Location: ../login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';
require_once '../../models/GuideModel.php';

$database = new Database();
$db = $database->connect();

$guideManager = new Guide($db);
$guides = $guideManager->getAllGuides() ;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $guideManager->createGuide($_POST['titre'], $_POST['contenu'], $_SESSION['user']['id']);
                break;
            case 'update':
                $guideManager->updateGuide($_POST['id'], $_POST['titre'], $_POST['contenu']);
                break;
            case 'delete':
                $guideManager->deleteGuide($_POST['id']);
                break;
        }
    }
    header('Location: manage_guides.php');
    exit;
}

include '../../views/templates/header.php';
include '../../views/templates/navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1>Gestion des Guides pour Bébé</h1>
    
    <!-- Formulaire pour créer un nouveau guide -->
    <h2 class="mt-4">Ajouter un nouveau guide</h2>
    <form action="manage_guides.php" method="POST">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="form-group">
            <label for="contenu">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-info">Ajouter le guide</button>
    </form>

    <!-- Liste des guides existants -->
    <h2 class="mt-4">Guides existants</h2>
    <div class="container mt-5">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="background: white">
                    <thead class="thead-dark">
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Contenu</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guides as $guide): ?>
            <tr>
                <td><?php echo htmlspecialchars($guide['titre']); ?></td>
                <td><?php echo htmlspecialchars($guide['auteur_nom']); ?></td>
                <td><?php echo htmlspecialchars($guide['contenu']); ?></td>
                <td><?php echo $guide['date_creation']; ?></td>
                <td>
                    <a href="edit_guide.php?id=<?php echo $guide['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <form action="manage_guides.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $guide['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce guide ?');">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
            </div>
    </div>

<?php include '../../views/templates/footer.php'; ?>