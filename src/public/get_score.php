<?php
session_start();
require_once '../views/templates/header.php';
require_once '../views/templates/navbar.php';

$score = $_GET['score'];
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
    <h1 class="text-center">Votre score : <?php echo htmlspecialchars($score); ?></h1>
    <hr>
    <br>
    <a href="quizzes.php" class="btn btn-info mt-3">Retour aux quiz</a>
</div>

<?php
require_once '../views/templates/footer.php';
?>