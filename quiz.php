<?php
session_start();
require_once 'functions/Database.php';
require_once 'functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

$quiz_id = $_GET['id'];
$quizData = $quiz->getQuizById($quiz_id);

require_once 'templates/header.php';
require_once 'templates/navbar.php';
?>
<style>
    h1,h2,h3 { text-align: center; }
    body {
        background-image: url('image/background.jpg');
        padding-top: 48px;
    }
    h1, .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>
<div class="container mt-5">
    <h1 class="text-center"><?php echo htmlspecialchars($quizData['titre']); ?></h1>
    
    <form id="quizForm" action="submit_quiz.php" method="post">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <div id="questionsContainer">
            <?php foreach ($quizData['questions'] as $questionIndex => $question) : ?>
                <div class="question">
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($question['question_text']); ?></label>
                        <div id="answers_<?php echo $questionIndex; ?>">
                            <?php foreach ($question['answers'] as $answerIndex => $answer) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" id="answer_<?php echo $questionIndex; ?>_<?php echo $answerIndex; ?>" value="<?php echo $answer['id']; ?>">
                                    <label class="form-check-label" for="answer_<?php echo $questionIndex; ?>_<?php echo $answerIndex; ?>">
                                        <?php echo htmlspecialchars($answer['answer_text']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-info mt-3">Valider</button>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
