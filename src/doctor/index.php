<?php
session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {     
    header('Location: ../public/login.php');     
    exit; 
}  

require_once '../../config/Database.php'; 
require_once '../models/ForumModel.php';
require_once '../models/AvisMedicauxModel.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Thread($db); 
$avisMedicaux = new AvisMedicaux($db);

// Récupérer les données  
$threads = $forum->getThreads(10);
$avis = $avisMedicaux->getDerniersAvis(5);

// Inclure la navigation docteur   
include_once '../views/templates/header.php'; 
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
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

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="../public/index.php">Tout pour un nouveau né</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../forum/indexforum.php">Forum</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="food_pres/manage_food_presentations.php">Gérer les conseils de nutrition</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="medical_adv/manage_medical_advice.php">Les avis médicaux</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="recipes/manage_recipes.php">Gérer les Recettes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile/my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

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
                    <a class="btn btn-outline-info" href="../forum/threads/thread.php?id=<?php echo $thread['id']; ?>">Voir la discussion</a>
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

<?php include_once '../views/templates/footer.php'; ?> 
