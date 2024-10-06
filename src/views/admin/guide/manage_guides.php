<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect();

$guide = new \Models\Guide($db);
$guideController = new \Controllers\GuideController($guide);

$guides = $guideController->getAllGuides();

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
                $guideController->createGuide($titre, $contenu, $_SESSION['user']['id']);
            }
            break;
        case 'update':
            if ($id && $titre && $contenu) {
                $guideController->updateGuide($id, $titre, $contenu);
            }
            break;
        case 'delete':
            if ($id) {
                $guideController->deleteGuide($id);
            }
            break;
        default:
            $_SESSION['error_message'] = "Action non valide.";
            break;
    }
    header('Location: /Portfolio/toutpourunnouveaune/admin/guide');
    exit;
}

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../templates/header.php';
include '../../templates/navbar_admin.php';
?>

<div class="container mt-5">
    <h1>Gestion des Guides pour Bébé</h1>

    <!-- Formulaire pour créer un nouveau guide -->
    <h2 class="mt-4">Ajouter un nouveau guide</h2>
    <form action="/Portfolio/toutpourunnouveaune/admin/guide" method="POST">
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

    <!-- Liste des guides existants sous forme de cartes -->
    <h2 class="mt-4">Guides existants</h2>
    <div class="row">
        <?php foreach ($guides as $guide): ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars_decode($guide['titre']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Par : <?php echo htmlspecialchars_decode($guide['auteur_nom']); ?></h6>
                        <p class="card-text"><?php echo htmlspecialchars_decode($guide['contenu']); ?></p>
                        <small class="text-muted">Créé le : <?php echo $guide['date_creation']; ?></small>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="/Portfolio/toutpourunnouveaune/admin/guide/edit/<?php echo htmlspecialchars($guide['id']); ?>" class="btn btn-sm btn-warning">Modifier</a>
                            
                            <form action="/Portfolio/toutpourunnouveaune/admin/guide" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($guide['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce guide ?');">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
