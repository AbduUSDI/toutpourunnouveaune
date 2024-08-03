<?php 
session_start();

include '../../config/Database.php';
include '../models/AvisMedicauxModel.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$avisMedicaux = new AvisMedicaux($db);
$avis = $avisMedicaux->getAll();

// Inclure la navigation admin 
include_once '../views/templates/header.php';
include_once '../views/templates/navbar.php'; 
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <br>
    <hr>    
    <h1 class="my-4">Consulter les avis médicaux</h1>
    <hr>
    <br>
    <p>Ces avis médicaux sont ici pour vous aider à mieux comprendre les petites choses nouvelles sur votre petit. N'ayez crainte vous pourrez poser vos question sur le forum, pour identifier un avis mettez dans l'objet les 5 chiffres au début du titre de chaque avis médicaux.</p>

    <!-- Rubrique Avis Médicaux -->
    <br>
    <hr>
    <h2>Derniers avis médicaux des Docteurs</h2>
    <hr>
    <br>
    <ul class="list-group mb-4">         
        <?php foreach ($avis as $avi): ?>
            <?php 
            // Obtenir le nom du médecin pour chaque avis
            $medecin = $avisMedicaux->getParId($avi['medecin_id']); 
            ?>
            <li class="list-group-item">
                <h5><?php echo htmlspecialchars($avi['titre']); ?></h5>
                <p>Médecin: <?php echo htmlspecialchars($medecin['nom_utilisateur']); ?></p>
                <p><?php echo htmlspecialchars($avi['contenu']); ?></p>
                <small class="text-muted">Date: <?php echo $avi['date_creation']; ?></small>
            </li>       
        <?php endforeach; ?>     
    </ul> 
</div> 

<?php include '../views/templates/footer.php'; ?>
