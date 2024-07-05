<?php 
include_once 'functions/database.php';
include_once 'MongoDB.php';
include_once 'functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$quizManager = new Quiz($db);
$quizzes = $quizManager->getAllQuizzesWithQuestions();

include 'templates/header.php';
include 'templates/navbar.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    padding-top: 58px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

</style>
<div class="container">
    <h1 class="my-4">Liste des Quiz</h1>
    <?php foreach ($quizzes as $quiz): ?>
        <div class="quiz">
            <h2><?php echo htmlspecialchars($quiz['titre']); ?></h2>
            <form class="quizForm" data-quiz-id="<?php echo $quiz['id']; ?>">
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="question">
                        <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                        <?php if (isset($question['options']) && is_array($question['options'])): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Option</th>
                                        <th>Choix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($question['options'] as $optionIndex => $option): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($option['answer_text']); ?></td>
                                            <td>
                                                <input type="radio" name="questions[<?php echo $index; ?>][answer]" value="<?php echo $optionIndex; ?>" required>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Aucune option disponible pour cette question.</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <button type="button" class="btn btn-primary" onclick="submitQuizForm(this)">Soumettre le Quiz</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<script>
function submitQuizForm(button) {
    const form = button.closest('form');
    const formData = new FormData(form);
    const quizId = form.getAttribute('data-quiz-id');
    formData.append('quiz_id', quizId);

    fetch('submit_quiz.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Quiz soumis avec succès! Votre score est: ' + data.score);
        } else {
            alert('Erreur lors de la soumission du quiz: ' + (data.error || 'Erreur inconnue.'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la soumission du quiz. Veuillez vérifier la console pour plus de détails.');
    });
}
</script>

<?php include 'templates/footer.php'; ?>