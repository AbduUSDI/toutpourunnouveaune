<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../config/Database.php';
require_once '../models/TrackingModel.php';
require_once '../models/ForumModel.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Thread($db); 
$tracking = new Tracking($db);

$threads = $forum->getThreads();
$dailyTracking = $tracking->getTracking();

include "../views/templates/header.php";
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
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

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="../public/index.php">Tout pour un nouveau né</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../forum/indexforum.php">Forum</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="daily_tracking/manage_daily_tracking.php">Rapport journalier bébé</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="family/my_childrens.php">Mes enfants</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile/my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

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
                    <a class="btn btn-outline-info" href="../forum/thread.php?id=<?php echo $thread['id']; ?>">Voir la discussion</a>
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

<?php include '../views/templates/footer.php'; ?>
