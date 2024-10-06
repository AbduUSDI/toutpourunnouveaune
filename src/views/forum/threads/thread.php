<?php

session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

require_once '../../../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();
$mongoClient = new \Database\MongoDBForum();

// Instanciation des modèles
$forum = new \Models\Forum($db);
$response = new \Models\Response($db);

// Instanciation des controleurs
$threadController = new \Controllers\ForumController($forum);
$responseController = new \Controllers\ResponseController($response);

$threadId = $_GET['id'];
$currentThread = $threadController->getThreadById($threadId);
$responses = $responseController->getResponsesByThreadId($threadId);

// Mise à jour des vues dans MongoDB
$viewsCollection = $mongoClient->getCollection('views');
$viewsCollection->updateOne(
    ['thread_id' => $threadId],
    ['$inc' => ['views' => 1]],
    ['upsert' => true]
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = $_POST['body'];
    $userId = $_SESSION['user']['id'];
    if ($responseController->createResponse($threadId, $userId, $body)) {
        header("Location: /Portfolio/toutpourunnouveaune/forum/thread/$threadId");
        exit;
    } else {
        $error = "Erreur lors de l'ajout de la réponse. Veuillez réessayer.";
    }
}

include_once '../templates/header.php';
include_once '../templates/navbar_forum.php';
?>

<div class="container mt-4">
    <h1 class="my-4"><?php echo htmlspecialchars($currentThread['title']); ?></h1>
    <p><?php echo htmlspecialchars($currentThread['body']); ?></p>
    <small class="text-muted">Par <?php echo htmlspecialchars($currentThread['author']); ?> le <?php echo $currentThread['created_at']; ?></small>

    <h2 class="my-4">Réponses</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <ul class="list-group mb-4">
        <?php foreach ($responses as $response): ?>
            <li class="list-group-item">
                <p><?php echo htmlspecialchars($response['body'], ENT_QUOTES, 'UTF-8'); ?></p>
                <small class="text-muted">Par <?php echo htmlspecialchars($response['author'], ENT_QUOTES, 'UTF-8'); ?> le <?php echo htmlspecialchars($response['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2 class="my-4">Ajouter une réponse</h2>
    <form action="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo $threadId; ?>" method="post">
        <div class="form-group">
            <label for="body">Votre réponse</label>
            <textarea class="form-control" id="body" name="body" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Publier</button>
    </form>
</div>

<?php include_once '../templates/footer.php'; ?>
