<?php 

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

require_once '../../../vendor/autoload.php';

$db = (new Database\DatabaseConnection())->connect();

$forum = new \Models\Forum($db); 
$tracking = new \Models\Tracking($db);

$forumController = new \Controllers\ForumController($forum);
$trackingController = new \Controllers\TrackingController($tracking);

$threads = $forumController->getThreads();
$dailyTracking = $trackingController->getAllTracking();

include "../templates/header.php";
include "../templates/navbar_parent.php";
?>

<div class="container mb-4">     
    <h1 class="my-4">Espace parent</h1>
      
    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <div class="list-group mt-4">         
        <?php if (empty($threads)): ?>
            <p>Aucune discussion disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($threads as $thread): ?>             
                <div class="mb-4">
                    <h5 class="list-group-item"><strong>Titre :</strong> <?php echo htmlspecialchars($thread['title']); ?></h5> 
                    <p class="list-group-item"><strong>Créé par :</strong> <?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['created_at']; ?>)</p>
                    <p class="list-group-item"><strong>Contenu :</strong> <?php echo htmlspecialchars($thread['body']); ?></p>
                    <a class="btn btn-outline-info" href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo $thread['id']; ?>">Voir la discussion</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Rubrique Rapport quotidien -->
    <h2>Rapport quotidien pour bébé</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>Date du jour</th>
                    <th>Heure tétée</th>
                    <th>Durée tétée</th>
                    <th>Heure change</th>
                    <th>Médicament</th>
                    <th>Notes supplémentaires</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dailyTracking)): ?>
                <tr>
                    <td colspan="7">Aucun rapport quotidien disponible pour le moment.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($dailyTracking as $track): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($track['date']); ?></td>
                        <td><?php echo htmlspecialchars($track['heure_repas']); ?></td>
                        <td><?php echo htmlspecialchars($track['duree_repas']); ?></td>
                        <td><?php echo htmlspecialchars($track['heure_change']); ?></td>
                        <td><?php echo htmlspecialchars($track['medicament']); ?></td>
                        <td><?php echo htmlspecialchars($track['notes']); ?></td>
                        <td><?php echo htmlspecialchars($track['date_creation']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 

<?php include '../templates/footer.php'; ?>
