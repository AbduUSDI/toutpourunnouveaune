<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require_once '../functions/Database.php';
require_once '../functions/User.php';

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = $_GET['id'];

$database = new Database();
$db = $database->connect();



$userManager = new User($db);

$user = $userManager->getUtilisateurParId($user_id);



if (!$user) {
    header('Location: manage_users.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    $userManager->updateUser($user_id, $email, $role_id, $username, $password);

    header('Location: manage_users.php');
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>


<div class="container">
    <h1 class="my-4">Modifier Utilisateur</h1>
    <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['nom_utilisateur']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
    <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="password">
        <div class="input-group-append">


            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
        <div class="form-group">
            <label for="role_id">Rôle</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="1" <?php if ($user['role_id'] == 1) echo 'selected'; ?>>Administrateur</option>
                <option value="2" <?php if ($user['role_id'] == 2) echo 'selected'; ?>>Docteur</option>
                <option value="3" <?php if ($user['role_id'] == 3) echo 'selected'; ?>>Parent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
<script>

   // Exécute le script une fois que le DOM est entièrement chargé

document.addEventListener('DOMContentLoaded', function() {

    // Obtenir les éléments par leur ID
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Ajouter un écouteur d'événements pour le clic sur l'icône

    togglePassword.addEventListener('click', function() {
        // Modifie le type de l'input entre 'password' et 'text' quand on clic sur l'icône
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Chargement de l'icône FontAwesome à l'intérieur de l'élément cliqué grâce à une balise nommée "i"
        const eyeIcon = this.querySelector('i');
        
        // Modifications des classes FontAwesome pour l'icône de l'œil (barré/non barré)
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

</script>
<?php include '../templates/footer.php'; ?>
