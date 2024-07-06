<?php 
include '../functions/Database.php';
include '../functions/Tracking.php';
include '../functions/Forum.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Forum($db); 

$tracking = new Tracking($db);

$threads = $forum->getDerniersThreads();

$dailyTracking = $tracking->getTracking();

include "../templates/header.php";
include "navbar_parent.php";
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container">     
    <h1 class="my-4">Espace parent</h1>
      
    <!-- Rubrique Forum -->     
    <h2>Derniers Threads du Forum</h2>     
    <ul class="list-group mb-4">         
        <?php foreach ($threads as $thread): ?>             
            <li class="list-group-item"><?php echo htmlspecialchars($thread['title']); ?> - <?php echo htmlspecialchars($thread['author']); ?> (<?php echo $thread['date_creation']; ?>)</li>         
        <?php endforeach; ?>     
    </ul>

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