<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../MongoDB.php';
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Forum.php';
require_once '../functions/AvisMedicaux.php';

// Connexion à la base de données MySQL

$database = new Database();
$db = $database->connect();

$mongoClient = new MongoDB();
$quiz = $mongoClient;

$forum = new Forum($db);
$avisMedicaux = new AvisMedicaux($db);

// Récupérer les données

$threads = $forum->getDerniersThreads();
$avis = $avisMedicaux->getDerniersAvis();

// Inclure la navigation admin

include_once 'navbar_admin.php';
include_once '../templates/header.php';
?>

<div class="container">
    <h1 class="my-4">Dashboard Admin</h1>

    <!-- Rubrique Forum -->
    <h2>Derniers Threads du Forum</h2>
    <ul>
        <?php foreach ($threads as $thread): ?>
            <li><?php echo htmlspecialchars($thread['title']); ?> - <?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['date_creation']; ?>)</li>
        <?php endforeach; ?>
    </ul>

    <!-- Rubrique Scores des Quizz -->
    <h2>Tableau des Scores des Quizz</h2>
    <ul>
        <?php foreach ($scores as $score): ?>
            <li><?php echo htmlspecialchars($score['parent_name']); ?>: <?php echo $score['score']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Rubrique Avis Médicaux -->
    <h2>Derniers avis médicaux des Docteurs</h2>
    <ul>
        <?php foreach ($avis as $avi): ?>
            <li><?php echo htmlspecialchars($avi['titre']); ?> - <?php echo htmlspecialchars($avi['medecin_id']); ?> - <?php echo htmlspecialchars($avi['contenu']); ?> - (<?php echo $avi['date_creation']; ?>) </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include_once '../templates/footer.php'; ?>
