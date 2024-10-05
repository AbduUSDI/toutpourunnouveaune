<?php
session_start();
require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';
require_once '../../models/ForumModel.php';
require_once '../../models/ResponseModel.php';

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Instanciation des modèles
$thread = new Thread($db);
$response = new Response($db);

// Récupération des threads
$threads = $thread->getThreads();

include_once '../templates/header.php';
include_once '../templates/navbar_forum.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour compenser le décalage causé par la navbar fixed-top */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1 class="my-4">Forum</h1>
    <div class="row">
        <div class="col-md-12">
            <h2>Derniers Threads</h2>
            <ul class="list-group mb-4">
                <?php if (empty($threads)): ?>
                    <li class="list-group-item">
                        <p>Aucun thread n'a encore été créé.</p>
                    </li>
                <?php else: ?>
                    <?php foreach ($threads as $thread): ?>
                        <li class="list-group-item">
                            <h5><a href="thread.php?id=<?php echo htmlspecialchars($thread['id']); ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                            <p><?php echo nl2br(htmlspecialchars($thread['body'])); ?></p>
                            <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo htmlspecialchars($thread['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>
