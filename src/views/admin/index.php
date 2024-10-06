<?php 
session_start(); 

// Vérification de l'authentification et des autorisations
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {     
    header('Location: /Portfolio/toutpourunnouveaune/login');     
    exit; 
}  

require_once '../../../vendor/autoload.php';

// Connexion à la base de données MySQL  
$db = (new Database\DatabaseConnection())->connect(); 

// Connexion à MongoDB
$mongoClient = new \Database\MongoDBConnection(); 
$quiz = $mongoClient; 
$scores = $quiz->getScoresParents();  

// Instancier les modèles
$user = new \Models\User($db); 
$avisMedicaux = new \Models\AvisMedicaux($db);  
$forum = new \Models\Forum($db);

// Instancier les contrôleurs
$userController = new \Controllers\UserController($db, $user);
$avisMedicauxController = new \Controllers\AvisMedicauxController($avisMedicaux);
$threadController = new \Controllers\ForumController($forum);

// Récupérer les données
$avis = $avisMedicauxController->getDerniersAvis();  
$threads = $threadController->getThreads();

// Extraire les IDs d'utilisateurs
$userIds = array_column($scores, 'user_id');  

// Récupérer les noms d'utilisateurs depuis MySQL
$usernames = $userController->getUsernames($db, $userIds);  

// Combiner les scores et les noms d'utilisateurs
foreach ($scores as &$score) {
    $userId = $score['user_id'];
    $score['nom_utilisateur'] = isset($usernames[$userId]) ? htmlspecialchars($usernames[$userId]) : 'Utilisateur inconnu';
}
unset($score);

// Trier les scores par ordre décroissant
usort($scores, function($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
});

// Calculer le total des scores
$totalScore = array_sum(array_column($scores, 'total_score'));

// Inclure les fichiers de navigation et de styles
include_once '../templates/header.php';
include_once '../templates/navbar_admin.php';
?>  

<div class="container mt-5">
    <br>
    <hr>     
    <h1 class="my-4">Dashboard Admin</h1>
    <hr>
    <br>
    <div class="row">
        <div class="col-md-12">
            <br>
            <hr> 
            <h2>Derniers Threads</h2>
            <hr>
            <br>
            <?php if (empty($threads)): ?>
                <p>Aucune discussion n'existe.</p>
            <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($threads as $thread): ?>
                        <li class="list-group-item">
                            <h5><a href="/Portfolio/toutpourunnouveaune/forum/thread/<?php echo htmlspecialchars($thread['id']); ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
                            <p><?php echo htmlspecialchars($thread['body']); ?></p>
                            <small class="text-muted">Par <?php echo htmlspecialchars($thread['author']); ?> le <?php echo htmlspecialchars($thread['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rubrique Scores des Quizz -->     
    <h2>Tableau des Scores des Quizz</h2>
    <div class="table-responsive">   
        <table class="table table-striped table-hover mb-4" style="background: white">
            <thead class="thead-dark">
                <tr>
                    <th>Utilisateur</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $score): ?>             
                    <tr>
                        <td><?php echo htmlspecialchars($score['nom_utilisateur'] ?? 'Utilisateur inconnu'); ?></td>
                        <td><?php echo htmlspecialchars($score['total_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>     
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?php echo htmlspecialchars($totalScore); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>  

    <!-- Rubrique Avis Médicaux -->     
    <h2>Derniers avis médicaux des Docteurs</h2>     
    <ul class="list-group mb-4">         
        <?php foreach ($avis as $avi): ?>             
            <li class="list-group-item">
                <h5><?php echo htmlspecialchars($avi['titre']); ?></h5>
                <p>Médecin: <?php echo htmlspecialchars($avi['medecin_id']); ?></p>
                <p><?php echo htmlspecialchars($avi['contenu']); ?></p>
                <small class="text-muted">Date: <?php echo htmlspecialchars($avi['date_creation']); ?></small>
            </li>         
        <?php endforeach; ?>     
    </ul> 
</div>  

<?php include_once '../templates/footer.php'; ?> 
