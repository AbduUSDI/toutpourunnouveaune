<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Guide.php';

// Vérifier si l'utilisateur est connecté et a les droits d'accès
if (!isset($_SESSION['user']) || ($_SESSION['user']['role_id'] != 1 && $_SESSION['user']['role_id'] != 2)) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$guideManager = new Guide($db);

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

include '../templates/header.php';
include 'navbar_admin.php';
?>

<div class="container mt-4">
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
        <button type="submit" class="btn btn-primary">Ajouter le guide</button>
    </form>

    <!-- Liste des guides existants -->
    <h2 class="mt-4">Guides existants</h2>
    <div class="container mt-5">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
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
            <?php foreach ($guideManager->getAllGuides() as $guide): ?>
            <tr>
                <td><?php echo htmlspecialchars($guide['titre']); ?></td>
                <td><?php echo htmlspecialchars($guide['auteur_nom']); ?></td>
                <td><?php echo htmlspecialchars($guide['contenu']); ?></td>
                <td><?php echo $guide['date_creation']; ?></td>
                <td>
                    <a href="edit_guide.php?id=<?php echo $guide['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
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

<?php include '../templates/footer.php'; ?>