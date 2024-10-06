<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}
require_once '../../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect();

$guideModel = new \Models\Guide($db);
$guideController = new \Controllers\GuideController($guideModel);

$guide_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$guide_id) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/guide');
    exit;
}

$guide = $guideController->getGuideById($guide_id);

if (!$guide) {
    header('Location: /Portfolio/toutpourunnouveaune/admin/guide');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: /Portfolio/toutpourunnouveaune/admin/guide/edit/' . $guide_id);
        exit;
    }
    
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

    if ($titre && $contenu) {
        $guideController->updateGuide($guide_id, $titre, $contenu);
        $_SESSION['success_message'] = "Guide mis à jour avec succès.";
        header('Location: /Portfolio/toutpourunnouveaune/admin/guide');
        exit;
    } else {
        $_SESSION['error_message'] = "Tous les champs sont requis.";
    }
}

// Générer un jeton CSRF pour protéger le formulaire
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../../templates/header.php';
include '../../templates/navbar_admin.php';
?>

<div class="container mt-5">
    <h1>Modifier le Guide</h1>
    <form action="/Portfolio/toutpourunnouveaune/admin/guide/edit/<?php echo htmlspecialchars($guide['id']); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars_decode($guide['titre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contenu">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="10" required><?php echo htmlspecialchars_decode($guide['contenu']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour le guide</button>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
