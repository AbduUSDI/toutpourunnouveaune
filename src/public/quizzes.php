<?php
session_start();
require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$quiz = new \Models\Quiz($db);
$quizController = new \Controllers\QuizController($quiz);

$quizzes = $quizController->getAllQuizzes();

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Tous nos Quiz disponibles</h1>
    <p>Afin d'améliorer vos chances d'être un meilleur parent, voici nos quiz qui sont régulièrement mis à jour pour vous aider afin de vous préparer à l'éducation de votre futur enfant.</p>
    <ul class="list-group mt-4">
        <?php if (empty($quizzes)): ?>
            <p>Aucun quiz disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($quizzes as $quiz): ?>
                <li class="list-group-item">
                    <a class="btn btn-outline-info" href="/Portfolio/toutpourunnouveaune/quiz/<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['titre']); ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<?php include '../views/templates/footer.php'; ?>
