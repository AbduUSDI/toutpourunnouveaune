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
$scores = $quiz->getScoresParents();  

$forum = new Forum($db); 
$usernames = new User($db); 
$avisMedicaux = new AvisMedicaux($db);  

// Récupérer les données  
$threads = $forum->getDerniersThreads(); 
$avis = $avisMedicaux->getDerniersAvis();  

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

// Inclure la navigation admin  
include_once 'navbar_admin.php'; 
include_once '../templates/header.php'; 
?>  

<div class="container">     
    <h1 class="my-4">Dashboard Admin</h1>
      
    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <ul class="list-group mb-4">         
        <?php foreach ($threads as $thread): ?>             
            <li class="list-group-item"><?php echo htmlspecialchars($thread['title']); ?> - <?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['date_creation']; ?>)</li>         
        <?php endforeach; ?>     
    </ul>
      
    <!-- Rubrique Scores des Quizz -->     
    <h2>Tableau des Scores des Quizz</h2>     
    <table class="table table-striped table-hover mb-4">
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
                    <td><?php echo $score['score']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>     
    </table>
      
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