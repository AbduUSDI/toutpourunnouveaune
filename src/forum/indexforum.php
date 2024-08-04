<?php
session_start();
require_once '../../config/Database.php';
require_once '../models/UserModel.php';
require_once '../models/ForumModel.php';
require_once '../../config/MongoDB.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$user = new User2($db);
$thread = new Thread($db);
$mongoClient = new MongoDBForum();

$threads = $thread->getThreads();

// Récupération des vues depuis MongoDB
$viewsCollection = $mongoClient->getCollection('views');
$activeThreads = $viewsCollection->find([], ['sort' => ['views' => -1], 'limit' => 5])->toArray();

$threadTitles = [];
foreach ($activeThreads as $activeThread) {
    $threadId = $activeThread['thread_id'];
    $currentThread = $thread->getThreadById($threadId);
    if ($currentThread) {
        $threadTitles[$threadId] = $currentThread['title'];
    }
}

include_once 'templates/header.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="indexforum.php">Tout pour un nouveau né - Forum</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../public/index.php">Retour sur tout-pour-un-nouveau-ne</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="indexforum.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="threads/add_thread.php">Créer une discussion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="threads/threads.php">Toutes les discussions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile/my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">Nous contacter</a>
            </li>
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Connexion</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>


<div class="container mt-5">
    <h1 class="my-4">Forum</h1>
    <div class="row">
        <div class="col-md-8">
            <h2>Derniers Threads</h2>
            <?php if (empty($threads)): ?>
        <p>Aucunes discussion n'existe.</p>
            <ul class="list-group mb-4">
                <?php else: ?>
                <?php foreach ($threads as $thread): ?>
                    <li class="list-group-item">
                        <h5><a href="threads/thread.php?id=<?php echo $thread['id']; ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                        <p><?php echo htmlspecialchars($thread['body']); ?></p>
                        <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo $thread['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        </div>
        <div class="col-md-4">
            <h2>Threads les plus actifs</h2>
            <?php if (empty($threads)): ?>
        <p>Aucunes discussion n'a été trouvée.</p>
            <ul class="list-group mb-4">
                <?php else: ?>
            <ul class="list-group mb-4">
                <?php foreach ($activeThreads as $activeThread): ?>
                    <li class="list-group-item">
                        <h5><a href="threads/thread.php?id=<?php echo $activeThread['thread_id']; ?>"><?php echo htmlspecialchars($threadTitles[$activeThread['thread_id']] ?? 'Titre inconnu'); ?></a></h5>
                        <small class="text-muted">Vues: <?php echo $activeThread['views']; ?></small>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
