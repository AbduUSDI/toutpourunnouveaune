<?php

session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {     
    header('Location: ../login.php');     
    exit; 
}  
 
require_once '../functions/Database.php'; 
require_once '../functions/Forum.php';
require_once '../functions/AvisMedicaux.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Thread($db); 

$avisMedicaux = new AvisMedicaux($db);

// Récupérer les données  
$threads = $forum->getThreads(); 
$avis = $avisMedicaux->getDerniersAvis();

// Inclure la navigation docteur  
include_once 'navbar_doctor.php'; 
include_once '../templates/header.php'; 
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
.mt-4 {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<div class="container mt-5">     
    <h1 class="my-4">Espace docteur</h1>
      
    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <div class="list-group mt-4">         
        <?php foreach ($threads as $thread): ?>             
            <div class="mb-4"><h5 class="list-group-item"><?php echo htmlspecialchars($thread['title']); ?></h5> 
            <p class="list-group-item"><?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['created_at']; ?>)</p>
            <p class="list-group-item"><?php echo htmlspecialchars($thread['body']); ?></p></div>
            <a class="btn btn-outline-info" href="../forum/thread.php?id=<?php echo $thread['id']; ?>">Voir la discussion</a>
        <?php endforeach; ?>     
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