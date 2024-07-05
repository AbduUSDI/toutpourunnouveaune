<?php 
include_once '../functions/database.php';
include_once '../MongoDB.php';
include_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$mongoClient = new MongoDB();
$quiz = $mongoClient;

$quizManager = new Quiz($db);

include '../templates/header.php';
include 'navbar_admin.php';
?>

<div class="container">
    <h1 class="my-4">Ajouter un Quiz</h1>
    <form id="quizForm">
        <div id="questionsContainer">
            <!-- Les questions seront ajoutées ici dynamiquement -->
        </div>
        <button type="button" class="btn btn-primary" onclick="addQuestion()">Ajouter une Question</button>
        <button type="submit" class="btn btn-success">Soumettre le Quiz</button>
    </form>
</div>

<script>
let questionCount = 0;

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

document.getElementById('quizForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('save_quiz.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quiz ajouté avec succès!');
            window.location.href = 'manage_quizzes.php';
        } else {
            alert('Erreur lors de l\'ajout du quiz.');
        }
    })
    .catch(error => console.error('Erreur:', error));
});
</script>

<?php include '../templates/footer.php'; ?>