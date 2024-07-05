<?php 
include_once '../functions/database.php';
include_once '../MongoDB.php';
include_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$mongoClient = new MongoDB();
$quiz = $mongoClient;

$quizManager = new Quiz($db);
// Récupérer tous les quiz avec leurs questions
$quizzes = $quizManager->getAllQuizzesWithQuestions();

include '../templates/header.php';
include 'navbar_admin.php';
?>

<div class="container">
    <h1 class="my-4">Gérer les Quiz</h1>
    <div class="table-responsive">
        <a href="add_quiz.php" class="btn btn-success mb-4">Ajouter un Quiz</a>
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Titre du quiz</th>
                    <th>Questions du quiz</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quiz['titre']); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($quiz['questions'] as $question): ?>
                                    <li><?php echo htmlspecialchars($question['question_text']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <!-- Boutons pour modifer ou supprimer un quiz, redirection vers de nouvelles pages -->
                            <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>