<?php

session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

require_once '../../../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

// Instanciation des modèles
$forum = new \Models\Forum($db);

// Instanciation des controleurs
$threadController = new \Controllers\ForumController($forum);

// Récupération des threads
$threads = $threadController->getThreads();

include_once '../templates/header.php';
include_once '../templates/navbar_forum.php';
?>

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
                            <h5><a href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo htmlspecialchars($thread['id']); ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
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
