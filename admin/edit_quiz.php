<?php 
include_once '../functions/database.php';
include_once '../MongoDB.php';
include_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quizManager = new Quiz($db);

if (isset($_GET['id'])) {
    $quizId = $_GET['id'];
    $quiz = $quizManager->getQuizById($quizId);
    $questions = $quizManager->getQuestionsByQuizId($quizId);
} else {
    die("ID de quiz non spécifié.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = $_POST['quiz_id'];
    $questions = $_POST['questions'];
    $quizManager->updateQuiz($quizId, $questions);
    header("Location: manage_quizzes.php");
    exit();
}

include '../templates/header.php';
include 'navbar_admin.php';
?>

<div class="container">
    <h1 class="my-4">Modifier le Quiz</h1>
    <form id="quizForm" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
        <div id="questionsContainer">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question">
                    <h3>Question <?php echo $index + 1; ?></h3>
                    <input type="text" name="questions[<?php echo $index; ?>][text]" value="<?php echo htmlspecialchars($question['question_text']); ?>" class="form-control mb-2" required>
                    <div class="optionsContainer" id="optionsContainer<?php echo $index; ?>">
                        <?php foreach ($question['options'] as $optionIndex => $option): ?>
                            <div class="option">
                                <input type="radio" name="questions[<?php echo $index; ?>][correct]" value="<?php echo $optionIndex; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?> required>
                                <input type="text" name="questions[<?php echo $index; ?>][options][<?php echo $optionIndex; ?>]" value="<?php echo htmlspecialchars($option['answer_text']); ?>" class="form-control mb-2" required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addOption(<?php echo $index; ?>)">Ajouter une Option</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-primary" onclick="addQuestion()">Ajouter une Question</button>
        <button type="submit" class="btn btn-success">Mettre à jour le Quiz</button>
    </form>
</div>

<script>
let questionCount = <?php echo count($questions); ?>;

function addQuestion() {
    questionCount++;
    const questionDiv = document.createElement('div');
    questionDiv.classList.add('question');
    questionDiv.innerHTML = `
        <h3>Question ${questionCount}</h3>
        <input type="text" name="questions[${questionCount}][text]" placeholder="Texte de la question" class="form-control mb-2" required>
        <div class="optionsContainer" id="optionsContainer${questionCount}">
            <!-- Les options seront ajoutées ici dynamiquement -->
        </div>
        <button type="button" class="btn btn-secondary" onclick="addOption(${questionCount})">Ajouter une Option</button>
    `;
    document.getElementById('questionsContainer').appendChild(questionDiv);
}

function addOption(questionId) {
    const optionsContainer = document.getElementById(`optionsContainer${questionId}`);
    const optionCount = optionsContainer.children.length + 1;
    const optionDiv = document.createElement('div');
    optionDiv.classList.add('option');
    optionDiv.innerHTML = `
        <input type="radio" name="questions[${questionId}][correct]" value="${optionCount}" required>
        <input type="text" name="questions[${questionId}][options][${optionCount}]" placeholder="Texte de l'option" class="form-control mb-2" required>
    `;
    optionsContainer.appendChild(optionDiv);
}
</script>

<?php include '../templates/footer.php'; ?>