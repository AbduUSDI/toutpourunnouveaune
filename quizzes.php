<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);
$quizzes = $quiz->getAllQuizzes();

require_once 'templates/header.php';
require_once 'templates/navbar.php';
?>

<div class="container mt-5">
    <h1 class="text-center">Quizzes Disponibles</h1>
    <ul class="list-group mt-4">
        <?php foreach ($quizzes as $quiz): ?>
            <li class="list-group-item">
                <a href="quiz.php?id=<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['titre']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
require_once 'templates/footer.php';
?>
