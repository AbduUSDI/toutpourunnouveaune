<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Profile.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->connect();
$user = new User($db);
$profile = new Profile($db);

$userId = $_SESSION['user']['id'];
$userProfile = $profile->getProfileByUserId($userId);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $date_naissance = $_POST['date_naissance'];
    $biographie = $_POST['biographie'];

    // Traitement du mot de passe
    $newPassword = $_POST['new_password'];
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->updatePassword($userId, $hashedPassword);
    }

    // Traitement de la photo de profil
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo_profil']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = uniqid() . '.' . $ext;
            $uploadPath = 'uploads/' . $newName;
            move_uploaded_file($_FILES['photo_profil']['tmp_name'], $uploadPath);
            $photo_profil = $uploadPath;
        }
    } else {
        $photo_profil = $userProfile['photo_profil'];
    }

    // Mise à jour du profil
    $profile->updateProfile($userId, $prenom, $nom, $date_naissance, $biographie, $photo_profil);

    // Redirection pour éviter la soumission multiple du formulaire
    header('Location: view_profile.php?updated=1');
    exit;
}

include '../templates/header.php';
include 'navbar_doctor.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1>Profil de <?php echo htmlspecialchars($_SESSION['user']['nom_utilisateur']); ?></h1>
    
    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
        <div class="alert alert-success">Votre profil a été mis à jour avec succès.</div>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userProfile['prenom'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($userProfile['nom'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo $userProfile['date_naissance'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="biographie">Biographie</label>
            <textarea class="form-control" id="biographie" name="biographie" rows="3"><?php echo htmlspecialchars($userProfile['biographie'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="photo_profil">Photo de profil</label>
            <input type="file" class="form-control-file" id="photo_profil" name="photo_profil">
            <?php if (!empty($userProfile['photo_profil'])): ?>
                <img src="<?php echo htmlspecialchars($userProfile['photo_profil']); ?>" alt="Photo de profil" class="mt-2" style="max-width: 200px;">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="new_password">Nouveau mot de passe (laissez vide pour ne pas changer)</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>