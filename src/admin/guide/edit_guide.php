<?php
session_start();
// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';
require_once '../../models/GuideModel.php';

$database = new Database();
$db = $database->connect();

$guideManager = new Guide($db);

$guide_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$guide_id) {
    header('Location: manage_guides.php');
    exit;
}

$guide = $guideManager->getGuideById($guide_id);

if (!$guide) {
    header('Location: manage_guides.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: edit_guide.php?id=' . $guide_id);
        exit;
    }
    
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

    if ($titre && $contenu) {
        $guideManager->updateGuide($guide_id, $titre, $contenu);
        $_SESSION['success_message'] = "Guide mis à jour avec succès.";
        header('Location: manage_guides.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Tous les champs sont requis.";
    }
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
    <h1>Modifier le Guide</h1>
    <form action="edit_guide.php?id=<?php echo htmlspecialchars($guide['id']); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($guide['titre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contenu">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="10" required><?php echo htmlspecialchars($guide['contenu']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour le guide</button>
    </form>
</div>

<?php include '../../views/templates/footer.php'; ?>
