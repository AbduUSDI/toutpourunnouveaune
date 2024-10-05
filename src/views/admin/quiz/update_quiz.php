<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/QuizModel.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quiz_id) {
    header('Location: manage_quizzes.php');
    exit;
}

$quizData = $quiz->getQuizById($quiz_id);

// Protection CSRF
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Erreur de sécurité : jeton CSRF invalide.";
        header('Location: update_quiz.php?id=' . $quiz_id);
        exit;
    }

    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $questions = $_POST['questions']; // Validation personnalisée nécessaire pour les tableaux complexes

    if ($titre && $questions) {
        $quiz->updateQuiz($quiz_id, $titre, $questions);
        header('Location: manage_quizzes.php');
        exit;
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include_once '../../views/templates/header.php';
include_once '../../views/templates/navbar_admin.php';
?>
<style>
    h1, h2, h3 {
        text-align: center;
    }

    body {
        background-image: url('../../../assets/image/background.jpg');
        padding-top: 48px;
    }

    h1, .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>
<div class="container mt-5">
    <h1>Modifier le Quiz</h1>
    <form id="quizForm" method="post" action="update_quiz.php?id=<?php echo htmlspecialchars($quiz_id); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group">
            <label for="titre">Titre du Quiz</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($quizData['titre']); ?>" required>
        </div>
        <div id="questionsContainer">
            <?php foreach ($quizData['questions'] as $questionIndex => $question) : ?>
                <div class="question">
                    <div class="form-group">
                        <label>Question</label>
                        <input type="text" class="form-control" name="questions[<?php echo $questionIndex; ?>][question_text]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                    </div>
                    <?php foreach ($question['answers'] as $answerIndex => $answer) : ?>
                        <div class="form-group">
                            <label>Réponse</label>
                            <input type="text" class="form-control" name="questions[<?php echo $questionIndex; ?>][answers][<?php echo $answerIndex; ?>][answer_text]" value="<?php echo htmlspecialchars($answer['answer_text']); ?>" required>
                            <label>Bonne réponse</label>
                            <input type="checkbox" name="questions[<?php echo $questionIndex; ?>][answers][<?php echo $answerIndex; ?>][is_correct]" value="1" <?php if ($answer['is_correct']) echo 'checked'; ?>>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" class="btn btn-info add-answer">Ajouter une réponse</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-primary add-question">Ajouter une question</button>
        <button type="submit" class="btn btn-success mt-3">Modifier le Quiz</button>
    </form>
</div>

<script>
    let questionIndex = <?php echo count($quizData['questions']); ?>;
    let answerIndex = <?php echo count($quizData['questions'][count($quizData['questions']) - 1]['answers']); ?>;

    document.querySelector('.add-question').addEventListener('click', function() {
        const questionTemplate = `
            <div class="question">
                <div class="form-group">
                    <label>Question</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][question_text]" required>
                </div>
                <div class="form-group">
                    <label>Réponses</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][answers][0][answer_text]" required>
                    <label>Bonne réponse</label>
                    <input type="checkbox" name="questions[${questionIndex}][answers][0][is_correct]" value="1">
                </div>
                <button type="button" class="btn btn-info add-answer">Ajouter une réponse</button>
            </div>`;
        document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', questionTemplate);
        questionIndex++;
        answerIndex = 1;
    });

    document.getElementById('questionsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('add-answer')) {
            const questionDiv = e.target.closest('.question');
            const questionId = Array.from(document.querySelectorAll('.question')).indexOf(questionDiv);
            const answerTemplate = `
                <div class="form-group">
                    <input type="text" class="form-control" name="questions[${questionId}][answers][${answerIndex}][answer_text]" required>
                    <label>Bonne réponse</label>
                    <input type="checkbox" name="questions[${questionId}][answers][${answerIndex}][is_correct]" value="1">
                </div>`;
            questionDiv.insertAdjacentHTML('beforeend', answerTemplate);
            answerIndex++;
        }
    });
</script>
<?php
require_once '../../views/templates/footer.php';
?>
