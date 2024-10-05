<?php 
session_start();

require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$avisMedicaux = new \Models\AvisMedicaux($db);
$avisMedicauxController = new \Controllers\AvisMedicauxController($avisMedicaux);
$avis = $avisMedicauxController->getAllAvis();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>

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
