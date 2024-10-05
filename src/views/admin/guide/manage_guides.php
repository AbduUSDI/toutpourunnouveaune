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
$guides = $guideManager->getAllGuides();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: manage_guides.php');
        exit;
    }

    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

    switch ($action) {
        case 'create':
            if ($titre && $contenu) {
                $guideManager->createGuide($titre, $contenu, $_SESSION['user']['id']);
            }
            break;
        case 'update':
            if ($id && $titre && $contenu) {
                $guideManager->updateGuide($id, $titre, $contenu);
            }
            break;
        case 'delete':
            if ($id) {
                $guideManager->deleteGuide($id);
            }
            break;
        default:
            $_SESSION['error_message'] = "Action non valide.";
            break;
    }
    header('Location: manage_guides.php');
    exit;
}

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../views/templates/header.php';
include '../../views/templates/navbar_admin.php';
?>

<style>
    h1, h2, h3 {
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

<div class="container mt-5">
    <h1>Gestion des Guides pour Bébé</h1>

    <!-- Formulaire pour créer un nouveau guide -->
    <h2 class="mt-4">Ajouter un nouveau guide</h2>
    <form action="manage_guides.php" method="POST">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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
                                <a href="edit_guide.php?id=<?php echo htmlspecialchars($guide['id']); ?>" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="manage_guides.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($guide['id']); ?>">
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
