<?php
session_start();
require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';
require_once '../../models/ProfileModel.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$database = new Database();
$db = $database->connect();
$user = new User($db);
$profile = new Profile($db);

$userId = $_SESSION['user']['id'];
$userProfile = $profile->getProfileByUserId($userId);

include '../../views/templates/header.php';
include '../../views/templates/navbar_admin.php';
?>
<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1>Profil de <?php echo htmlspecialchars($_SESSION['user']['nom_utilisateur']); ?></h1>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($userProfile['photo_profil'])): ?>
                <img src="../../../assets/uploads/<?php echo htmlspecialchars($userProfile['photo_profil']); ?>" alt="Photo de profil" class="img-thumbnail mb-3" style="max-width: 200px;">
            <?php endif; ?>
            
            <h5 class="card-title"><?php echo htmlspecialchars($userProfile['prenom'] . ' ' . $userProfile['nom']); ?></h5>
            <p class="card-text"><strong>Date de naissance:</strong> <?php echo $userProfile['date_naissance'] ?? 'Non renseignée'; ?></p>
            <p class="card-text"><strong>Biographie:</strong> <?php echo nl2br(htmlspecialchars($userProfile['biographie'] ?? 'Aucune biographie')); ?></p>
            
            <?php
            // Afficher des informations spécifiques selon le rôle
            switch ($_SESSION['user']['role_id']) {
                case 1:
                    echo "<p><strong>Rôle:</strong> Administrateur</p>";
                    break;
                case 2:
                    echo "<p><strong>Rôle:</strong> Docteur</p>";
                    // Ajoutez ici d'autres informations spécifiques aux docteurs si nécessaire
                    break;
                case 3:
                    echo "<p><strong>Rôle:</strong> Parent</p>";
                    // Ajoutez ici d'autres informations spécifiques aux parents si nécessaire
                    break;
            }
            ?>
            
            <a href="edit_profile.php" class="btn btn-info">Modifier le profil</a>
        </div>
    </div>
</div>

<?php include '../../views/templates/footer.php'; ?>
