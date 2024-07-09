<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

include '../functions/Database.php';
include '../functions/Tracking.php';
include '../functions/Forum.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Thread($db); 

$tracking = new Tracking($db);

$threads = $forum->getThreads();

$dailyTracking = $tracking->getTracking();

include "../templates/header.php";
include "navbar_parent.php";
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
.mt-4 {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<div class="container mb-4">     
    <h1 class="my-4">Espace parent</h1>
      
    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <div class="list-group mt-4">         
        <?php foreach ($threads as $thread): ?>             
            <div class="mb-4"><h5 class="list-group-item"><?php echo htmlspecialchars($thread['title']); ?></h5> 
            <p class="list-group-item"><?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['created_at']; ?>)</p>
            <p class="list-group-item"><?php echo htmlspecialchars($thread['body']); ?></p></div>
            <a class="btn btn-outline-info" href="../forum/thread.php?id=<?php echo $thread['id']; ?>">Voir la discussion</a>
        <?php endforeach; ?>     
        </div>

    <h2>Rapport quotidien pour bébé</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>Utilisateur</th>
                    <th>Date du jour</th>
                    <th>Heure tétée</th>
                    <th>Durée tétée</th>
                    <th>Heure change</th>
                    <th>Medicament</th>
                    <th>Notes supplémentaires</th>
                    <th>Date de création de suivi quotidien</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($dailyTracking as $track): ?>
            <tr>
                <td><?php echo htmlspecialchars($track['utilisateur_id']); ?></td>
                <td><?php echo htmlspecialchars($track['date']); ?></td>
                <td><?php echo htmlspecialchars($track['heure_repas']); ?></td>
                <td><?php echo htmlspecialchars($track['duree_repas']); ?></td>
                <td><?php echo htmlspecialchars($track['heure_change']); ?></td>
                <td><?php echo htmlspecialchars($track['medicament']); ?></td>
                <td><?php echo htmlspecialchars($track['notes']); ?></td>
                <td>(<?php echo $track['date_creation']; ?>)</td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 

<?php include '../templates/footer.php'; ?>