<?php
session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {     
    header('Location: /Portfolio/toutpourunnouveaune/login');     
    exit; 
}

require_once '../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 

$forum = new \Models\Forum($db); 
$avisMedicaux = new \Models\AvisMedicaux($db);

$forumController = new \Controllers\ForumController($forum);
$avisMedicauxController = new \Controllers\AvisMedicauxController($avisMedicaux);

// Récupérer les données  
$threads = $forumController->getThreads(1);
$avis = $avisMedicauxController->getDerniersAvis(5);

// Inclure la navigation docteur   
include_once '../templates/header.php';
include_once '../templates/navbar_doctor.php';
?>

<div class="container mt-5">     
    <h1 class="my-4">Espace docteur</h1>

    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <div class="list-group mt-4">         
        <?php foreach ($threads as $thread): ?>             
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo htmlspecialchars($thread['title']); ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Auteur:</strong> <?php echo htmlspecialchars($thread['author']); ?></p>
                    <p><?php echo htmlspecialchars($thread['body']); ?></p>
                    <p><small class="text-muted"><?php echo $thread['created_at']; ?></small></p>
                    <a class="btn btn-outline-info" href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo $thread['id']; ?>">Voir la discussion</a>
                </div>
            </div>
        <?php endforeach; ?>     
    </div>
      
    <!-- Rubrique Avis Médicaux -->     
    <h2>Derniers avis médicaux des Docteurs</h2>     
    <ul class="list-group mb-4">         
        <?php foreach ($avis as $avi): ?>             
            <li class="list-group-item">
                <h5><?php echo htmlspecialchars($avi['titre']); ?></h5>
                <p><strong>Médecin:</strong> <?php echo htmlspecialchars($avi['medecin_id']); ?></p>
                <p><?php echo htmlspecialchars($avi['contenu']); ?></p>
                <small class="text-muted">Date: <?php echo $avi['date_creation']; ?></small>
            </li>         
        <?php endforeach; ?>     
    </ul> 
</div>  

<?php include_once '../templates/footer.php'; ?> 
