<?php
session_start();
require_once 'templates/header.php';
require_once 'templates/navbar.php';

$score = $_GET['score'];
?>

<div class="container mt-5">
    <h1 class="text-center">Votre score : <?php echo htmlspecialchars($score); ?></h1>
    <a href="quizzes.php" class="btn btn-primary mt-3">Retour aux quiz</a>
</div>

<?php
require_once 'templates/footer.php';
?>
