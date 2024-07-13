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
    body {
        background-image: url('image/background.jpg');
        background-size: cover;
        padding-top: 48px;
        font-family: 'Arial', sans-serif;
        color: #333;
    }
    .container {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h1 {
        font-size: 2.5em;
        margin-bottom: 20px;
        color: #007bff;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-check-input {
        margin-right: 10px;
    }
    .form-check-label {
        display: inline-block;
        margin-bottom: 10px;
    }
    .btn-info {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 20px;
        font-size: 1.2em;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .btn-info:hover {
        background-color: #0056b3;
        border-color: #004085;
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
