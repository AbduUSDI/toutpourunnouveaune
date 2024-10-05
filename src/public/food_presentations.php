<?php 
require '../../vendor/autoload.php';
session_start();

$database = new \Database\DatabaseConnection();
$db = $database->connect();

$foodPresentation = new \Models\FoodPresentation($db);
$presentation = new \Controllers\FoodPresentationController($foodPresentation);

$presentations = $presentation->getAllPresentations();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>

<div class="container mt-4">
    <br><hr>
    <h1 class="mb-4">Nos conseils de nutrition</h1>
    <hr><br>
    <p>Ces conseils sont ici pour vous aider à mieux gérer votre régime alimentaire pendant, avant ou après votre grossesse.</p>
    <br><hr>
    <h2>Conseils de nutrition</h2>
    <hr><br>     
    <ul class="list-group mb-4">         
        <?php foreach ($presentations as $advice): ?>             
            <li class="list-group-item">
                <h5><?php echo htmlspecialchars($advice['titre']); ?></h5>
                <p>Médecin: <?php echo htmlspecialchars($advice['contenu']); ?></p>
                <p><?php echo htmlspecialchars($advice['groupe_age']); ?></p>
                <small class="text-muted">Date: <?php echo $advice['date_creation']; ?></small>
            </li>         
        <?php endforeach; ?>     
    </ul> 

</div>

<?php include '../views/templates/footer.php'; ?>