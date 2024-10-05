<?php 
session_start(); 

// Vérification de l'authentification et des autorisations
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {     
    header('Location: ../public/login.php');     
    exit; 
}  

// Inclure les fichiers requis avec vérification de l'existence
require_once '../../config/Database.php'; 
require_once '../../config/MongoDB.php'; 
require_once '../models/UserModel.php'; 
require_once '../models/AvisMedicauxModel.php'; 
require_once '../models/ForumModel.php';

// Connexion à la base de données MySQL
$database = new Database(); 
$db = $database->connect();  

// Connexion à MongoDB
$mongoClient = new MongoDB(); 
$quiz = $mongoClient; 
$scores = $quiz->getScoresParents();  

// Instancier les modèles
$userModel = new User($db); 
$avisMedicauxModel = new AvisMedicaux($db);  
$threadModel = new Thread($db);

// Récupérer les données
$avis = $avisMedicauxModel->getDerniersAvis();  
$threads = $threadModel->getThreads();

// Extraire les IDs d'utilisateurs
$userIds = array_column($scores, 'user_id');  

// Récupérer les noms d'utilisateurs depuis MySQL
$usernames = $userModel->getUsernames($db, $userIds);  

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
include_once '../views/templates/header.php'; 
?>  

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="../../index.php">Tout pour un nouveau né</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil / Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users/manage_users.php">Gérer Utilisateurs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="guide/manage_guides.php">Gérer les guides</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quiz/manage_quizzes.php">Gérer les Quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="recipes/manage_recipes.php">Gérer les Recettes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile/my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="comments/manage_comment.php">Gérer commentaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

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
                            <h5><a href="../forum/thread.php?id=<?php echo htmlspecialchars($thread['id']); ?>"><?php echo htmlspecialchars($thread['title']); ?></a></h5>
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

<?php include_once '../views/templates/footer.php'; ?> 
