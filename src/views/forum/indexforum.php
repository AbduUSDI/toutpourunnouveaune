<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

require_once '../../../vendor/autoload.php';

// Instance Database
$db = (new Database\DatabaseConnection())->connect();
$mongoClient = new \Database\MongoDBForum();

// Instance Modèles
$user = new \Models\UserTwo($db);
$forum = new \Models\Forum($db);

// Instance Controleurs
$userController = new \Controllers\UserTwoController($user);
$threadController = new \Controllers\ForumController($forum);

$viewsCollection = $mongoClient->getCollection('views');

// Récupération des threads les plus récents et les plus vus
$threads = $threadController->getThreads();
$activeThreads = $viewsCollection->find([], ['sort' => ['views' => -1], 'limit' => 5])->toArray();

// Construction de la liste des titres des threads les plus vus
$threadTitles = [];
foreach ($activeThreads as $activeThread) {
    $threadId = $activeThread['thread_id'];
    $currentThread = $threadController->getThreadById($threadId);
    if ($currentThread) {
        $threadTitles[$threadId] = $currentThread['title'];
    }
}

include_once 'templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<div class="container mt-5">
    <h1 class="my-4">Forum</h1>
    <div class="row">
        <div class="col-md-8">
            <h2>Derniers Threads</h2>
            <?php if (empty($threads)): ?>
                <p>Aucune discussion n'existe pour le moment.</p>
            <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($threads as $thread): ?>
                        <li class="list-group-item">
                            <h5><a href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo htmlspecialchars($thread['id']); ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                            <p><?php echo nl2br(htmlspecialchars($thread['body'])); ?></p>
                            <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo htmlspecialchars($thread['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <h2>Threads les plus actifs</h2>
            <?php if (empty($activeThreads)): ?>
                <p>Aucun thread actif n'a été trouvé.</p>
            <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($activeThreads as $activeThread): ?>
                        <li class="list-group-item">
                            <h5><a href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo htmlspecialchars($activeThread['thread_id']); ?>"><?php echo htmlspecialchars($threadTitles[$activeThread['thread_id']] ?? 'Titre inconnu'); ?></a></h5>
                            <small class="text-muted">Vues : <?php echo htmlspecialchars($activeThread['views']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
