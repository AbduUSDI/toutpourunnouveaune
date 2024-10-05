<?php
session_start();

require_once '../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$quiz = new \Models\Quiz($db);
$quizController = new \Controllers\QuizController($quiz);

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$quizData = $quizController->getQuizById($quiz_id);

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include '../views/templates/header.php';
include '../views/templates/navbar.php';
?>

<div class="container mt-5">
    <h1 class="text-center"><?php echo htmlspecialchars($quizData['titre']); ?></h1>
    <form id="quizForm" action="/Portfolio/toutpourunnouveaune/submit_quiz" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <div id="questionsContainer">
            <?php foreach ($quizData['questions'] as $questionIndex => $question) : ?>
                <div class="question">
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($question['question_text']); ?></label>
                        <div id="answers_<?php echo $questionIndex; ?>">
                            <?php foreach ($question['answers'] as $answerIndex => $answer) : ?>
                                <label class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" id="answer_<?php echo $questionIndex; ?>_<?php echo $answerIndex; ?>" value="<?php echo $answer['id']; ?>">
                                    <span class="checkmark "></span>
                                    <?php echo htmlspecialchars($answer['answer_text']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-info mt-3">Valider</button>
    </form>
</div>

<?php include '../views/templates/footer.php'; ?>
