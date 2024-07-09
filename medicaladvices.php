<?php 
session_start();

include 'functions/Database.php';
include 'functions/AvisMedicaux.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$avisMedicaux = new AvisMedicaux($db);

$avis = $avisMedicaux->getAll();

// Inclure la navigation admin 
include_once 'templates/header.php';
include_once 'templates/navbar.php'; 
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">     
    <h1 class="my-4">Consulter les avis médicaux</h1>
    <p>Ces avis médicaux sont ici pour vous aider à mieux comprendre les petites choses nouvelles sur votre petit. N'ayez crainte vous pourrez poser vos question sur le forum, pour identifier un avis mettez dans l'objet les 5 chiffres au début du titre de chaque avis médicaux.</p>

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

<?php include 'templates/footer.php';