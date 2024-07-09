<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $questions = $_POST['questions'];

    $quiz_id = $quiz->addQuiz($titre, $questions);
    header('Location: manage_quizzes.php');
    exit;
}

require_once '../templates/header.php';
require_once 'navbar_admin.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1>Créer un nouveau Quiz</h1>
    <form id="quizForm" method="post" action="add_quiz.php">
        <div class="form-group">
            <label for="titre">Titre du Quiz</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div id="questionsContainer">
            <div class="question">
                <div class="form-group">
                    <label>Question</label>
                    <input type="text" class="form-control" name="questions[0][question_text]" required>
                </div>
                <div class="answersContainer">
                    <div class="form-group answer">
                        <label>Réponse</label>
                        <input type="text" class="form-control" name="questions[0][answers][0][answer_text]" required>
                        <label>Bonne réponse</label>
                        <input type="checkbox" name="questions[0][answers][0][is_correct]" value="1">
                    </div>
                </div>
                <button type="button" class="btn btn-info add-answer">Ajouter une réponse</button>
            </div>
        </div>
        <button type="button" class="btn btn-primary add-question">Ajouter une question</button>
        <button type="submit" class="btn btn-success mt-3">Créer le Quiz</button>
    </form>
</div>

<script>
    let questionIndex = 1;

    document.querySelector('.add-question').addEventListener('click', function() {
        const questionTemplate = `
            <div class="question">
                <div class="form-group">
                    <label>Question</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][question_text]" required>
                </div>
                <div class="answersContainer">
                    <div class="form-group answer">
                        <label>Réponse</label>
                        <input type="text" class="form-control" name="questions[${questionIndex}][answers][0][answer_text]" required>
                        <label>Bonne réponse</label>
                        <input type="checkbox" name="questions[${questionIndex}][answers][0][is_correct]" value="1">
                    </div>
                </div>
                <button type="button" class="btn btn-info add-answer">Ajouter une réponse</button>
            </div>`;
        document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', questionTemplate);
        questionIndex++;
    });

    document.getElementById('questionsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('add-answer')) {
            const questionDiv = e.target.closest('.question');
            const answersContainer = questionDiv.querySelector('.answersContainer');
            const answerIndex = answersContainer.querySelectorAll('.answer').length;
            const questionIndex = Array.from(document.querySelectorAll('.question')).indexOf(questionDiv);
            const answerTemplate = `
                <div class="form-group answer">
                    <label>Réponse</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" required>
                    <label>Bonne réponse</label>
                    <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="1">
                </div>`;
            answersContainer.insertAdjacentHTML('beforeend', answerTemplate);
        }
    });
</script>

<?php
require_once '../templates/footer.php';
?>
