<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/QuizModel.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);
$quizzes = $quiz->getAllQuizzes();

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
    <h1 class="mb-4">Tous nos Quiz disponibles</h1>
    <p>Afin d'améliorer vos chances d'être un meilleur parent, voici nos quiz qui sont régulièrement mis à jour pour vous aider afin de vous préparer à l'éducation de votre futur enfant.</p>
    <ul class="list-group mt-4">
        <?php if (empty($quizzes)): ?>
            <p>Aucun quiz disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($quizzes as $quiz): ?>
                <li class="list-group-item">
                    <a class="btn btn-outline-info" href="quiz.php?id=<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['titre']); ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<?php include '../views/templates/footer.php'; ?>
