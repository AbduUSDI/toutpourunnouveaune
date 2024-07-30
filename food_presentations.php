<?php 

session_start();

include_once 'functions/Database.php';
include_once 'functions/FoodPresentation.php';

$database = new Database();
$db = $database->connect();

$presentation = new FoodPresentation($db);

$presentations = $presentation->getAll();

include 'templates/header.php';
include 'templates/navbar.php';
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
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

<?php include 'templates/footer.php'; ?>