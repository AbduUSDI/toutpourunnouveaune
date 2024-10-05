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

$quizzes = $quiz->getAllQuizzes();

// Générer un jeton CSRF pour protéger les actions de suppression
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
    <h1 class="my-4">Gestion des Quiz</h1>
    <a href="add_quiz.php" class="btn btn-info">Ajouter un nouveau quiz</a>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-4" style="background: white">
            <thead class="thead-dark">
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
                            <a href="update_quiz.php?id=<?php echo htmlspecialchars($quiz['id']); ?>" class="btn btn-warning">Modifier</a>
                            <form action="delete_quiz.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($quiz['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?');">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../../views/templates/footer.php'; ?>
