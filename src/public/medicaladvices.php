<?php 
session_start();

include '../../config/Database.php';
include '../models/AvisMedicauxModel.php';

$database = new Database(); 
$db = $database->connect(); 

$avisMedicaux = new AvisMedicaux($db);
$avis = $avisMedicaux->getAll();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
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
    <h1 class="my-4">Consulter les avis médicaux</h1>
    <p>Ces avis médicaux sont ici pour vous aider à mieux comprendre les petites choses nouvelles sur votre petit.</p>
    <ul class="list-group mb-4">
        <?php if (empty($avis)): ?>
            <p>Aucun avis médical disponible pour le moment.</p>
        <?php else: ?>
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
        <?php endif; ?>
    </ul> 
</div>

<?php include '../views/templates/footer.php'; ?>
