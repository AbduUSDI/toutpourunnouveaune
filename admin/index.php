<?php 
session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {     
    header('Location: ../login.php');     
    exit; 
}  

require_once '../MongoDB.php'; 
require_once '../functions/Database.php'; 
require_once '../functions/User.php'; 
require_once '../functions/AvisMedicaux.php'; 
require_once '../forum/functions/Database.php'; 
require_once '../forum/functions/Thread.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect();  

$database2 = new Database2();
$db2 = $database2->connect();

$mongoClient = new MongoDB(); 
$quiz = $mongoClient; 
$scores = $quiz->getScoresParents();  

$usernames = new User($db); 
$avisMedicaux = new AvisMedicaux($db);  
$thread = new Thread($db2);

// Récupérer les données  

$avis = $avisMedicaux->getDerniersAvis();  
$threads = $thread->getThreads();

// Extraire les IDs d'utilisateurs 
$userIds = array_column($scores, 'user_id');  

// Récupérer les noms d'utilisateurs depuis MySQL 
$username = $usernames->getUsernames($db,$userIds);  

// Combiner les scores et les noms d'utilisateurs
foreach ($scores as &$score) {
    $userId = $score['user_id'];
    $score['nom_utilisateur'] = isset($username[$userId]) ? $username[$userId] : 'Utilisateur inconnu';
}
unset($score);

// Trier les scores par ordre décroissant
usort($scores, function($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
});

// Calculer le total des scores
$totalScore = array_sum(array_column($scores, 'total_score'));

// Inclure la navigation admin  
include_once 'navbar_admin.php'; 
include_once '../templates/header.php'; 
?>  
<style>
h1, h2, h3 {
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
<div class="container">     
    <h1 class="my-4">Dashboard Admin</h1>
      
    <div class="container mt-5">
    <h1 class="my-4">Forum</h1>
    <div class="row">
        <div class="col-md-8">
            <h2>Derniers Threads</h2>
            <?php if (empty($threads)): ?>
        <p>Aucunes discussion n'existe.</p>
            <ul class="list-group mb-4">
                <?php else: ?>
                <?php foreach ($threads as $thread): ?>
                    <li class="list-group-item">
                        <h5><a href="../forum/thread.php?id=<?php echo $thread['id']; ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                        <p><?php echo htmlspecialchars($thread['body']); ?></p>
                        <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo $thread['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        </div>
      </div>
    <!-- Rubrique Scores des Quizz -->     
    <h2>Tableau des Scores des Quizz</h2>
    <div class="table-responsive">   
    <table class="table table-striped table-hover mb-4" style="background: white">
        <thead class="thead-dark">
            <tr>
                <th>Utilisateur</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scores as $score): ?>             
                <tr>
                    <td><?php echo htmlspecialchars($score['nom_utilisateur'] ?? 'Utilisateur inconnu'); ?></td>
                    <td><?php echo $score['total_score']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>     
        <tfoot>
            <tr>
                <th>Total</th>
                <th><?php echo $totalScore; ?></th>
            </tr>
        </tfoot>
    </table>
    </div>  
    <!-- Rubrique Avis Médicaux -->     
    <h2>Derniers avis médicaux des Docteurs</h2>     
    <ul class="list-group mb-4">         
        <?php foreach ($avis as $avi): ?>             
            <li class="list-group-item">
                <h5><?php echo htmlspecialchars($avi['titre']); ?></h5>
                <p>Médecin: <?php echo htmlspecialchars($avi['medecin_id']); ?></p>
                <p><?php echo htmlspecialchars($avi['contenu']); ?></p>
                <small class="text-muted">Date: <?php echo $avi['date_creation']; ?></small>
            </li>         
        <?php endforeach; ?>     
    </ul> 
</div>  

<?php include_once '../templates/footer.php'; ?> 
