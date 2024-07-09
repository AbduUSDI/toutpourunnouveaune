<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Guide.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();



$guideManager = new Guide($db);

if (!isset($_GET['id'])) {
    header('Location: manage_guides.php');
    exit;
}

$guide = $guideManager->getGuideById($_GET['id']);

if (!$guide) {
    header('Location: manage_guides.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guideManager->updateGuide($_GET['id'], $_POST['titre'], $_POST['contenu']);
    header('Location: manage_guides.php');
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1>Modifier le Guide</h1>
    <form action="edit_guide.php?id=<?php echo $guide['id']; ?>" method="POST">
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

<?php include '../templates/footer.php'; ?>