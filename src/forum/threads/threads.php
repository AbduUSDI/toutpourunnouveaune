<?php
session_start();
require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';
require_once '../../models/ForumModel.php';
require_once '../../models/ResponseModel.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$thread = new Thread($db);
$response = new Response($db);

$threads = $thread->getThreads();

include_once '../templates/header.php';
include_once '../templates/navbar_forum.php';
?>

<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
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
                <?php foreach ($threads as $thread): ?>
                    <li class="list-group-item">
                        <h5><a href="thread.php?id=<?php echo $thread['id']; ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                        <p><?php echo htmlspecialchars($thread['body']); ?></p>
                        <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo $thread['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>


