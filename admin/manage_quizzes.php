<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../MongoDB.php';
require_once '../functions/Database.php';
require_once '../functions/Quiz.php';

$database = new Database();
$db = $database->connect();

$mongoClient = new MongoDB();
$quiz = new Quiz($db);

$quizzes = $quiz->getAllQuizzes();

include_once 'navbar_admin.php';
include_once '../templates/header.php';
?>

<div class="container">
    <h1 class="my-4">Gestion des Quizzes</h1>
    <a href="add_quiz.php" class="btn btn-primary">Ajouter un nouveau quiz</a>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?php echo htmlspecialchars($quiz['id']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['titre']); ?></td>
                    <td><?php echo htmlspecialchars($quiz['created_at']); ?></td>
                    <td>
                        <a href="update_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-warning">Modifier</a>
                        <a href="delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once '../templates/footer.php'; ?>
