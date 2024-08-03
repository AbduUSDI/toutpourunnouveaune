<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/QuizModel.php';

$database = new Database();
$db = $database->connect();

$quiz = new Quiz($db);

$quiz_id = $_GET['id'];
$quizData = $quiz->getQuizById($quiz_id);

require_once '../views/templates/header.php';
require_once '../views/templates/navbar.php';
?>

<style>
    body {
        background-image: url('../../assets/image/background.jpg');
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
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-check {
        margin-bottom: 15px;
        position: relative;
        padding-left: 35px;
        cursor: pointer;
        font-size: 18px;
        user-select: none;
    }
    .form-check-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    .form-check:hover input ~ .checkmark {
        background-color: #ccc;
    }
    .form-check input:checked ~ .checkmark {
        background-color: #2196F3;
        transform: scale(1.1);
    }
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }
    .form-check input:checked ~ .checkmark:after {
        display: block;
    }
    .form-check .checkmark:after {
        top: 9px;
        left: 9px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
    }
    .btn-info {
        background-color: #007bff;
        border-color: #007bff;
        padding: 12px 24px;
        font-size: 1.2em;
        transition: all 0.3s ease;
        border-radius: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .btn-info:hover {
        background-color: #0056b3;
        border-color: #004085;
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0,0,0,0.15);
    }
    .btn-info:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center"><?php echo htmlspecialchars($quizData['titre']); ?></h1>
    <hr>
    <br>
    <form id="quizForm" action="submit_quiz.php" method="post">
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
                                    <span class="checkmark"></span>
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

<?php require_once '../views/templates/footer.php'; ?>