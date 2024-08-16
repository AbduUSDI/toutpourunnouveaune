<?php
session_start();

require_once '../../config/Database.php';
require_once '../models/UserModel.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['mot_de_passe'];

    $userData = $user->getUtilisateurParEmail($email);

    if ($userData && password_verify($password, $userData['mot_de_passe'])) {
        $_SESSION['user'] = $userData;
        if ($userData['role_id'] == 1 || $userData['role_id'] == 2 || $userData['role_id'] == 3) {
            header('Location: index.php');
        } else {
            header('Location: login.php');
        }
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}

include_once '../views/templates/header.php';
include_once '../views/templates/navbar.php';

if (isset($_SESSION['reset_message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.getElementById('resetPasswordMessage');
            messageElement.textContent = '" . $_SESSION['reset_message'] . "';
            messageElement.className = 'alert alert-success';
            messageElement.style.display = 'block';
        });
    </script>";
    unset($_SESSION['reset_message']);
}
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 48px;
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <div id="resetPasswordMessage" class="alert" style="display: none;"></div>
    <br>
    <hr>
    <h1 class="my-4">Connexion</h1>
    <hr>
    <br>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="mot_de_passe" autocomplete="current-password" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-info" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-info" name="login">Se connecter</button>
    </form>
    <hr>
    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#registerModal">S'inscrire</button>
    <hr>
    <button class="btn btn-outline-warning" data-toggle="modal" data-target="#forgotPasswordModal">Mot de passe oublié ?</button>
    <hr>  
</div>

<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Mot de passe oublié ?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm" method="post" action="forgot_password.php">
                    <div class="form-group">
                        <label for="forgotEmail">Email</label>
                        <input type="email" class="form-control" id="forgotEmail" name="forgotEmail" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">S'inscrire</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registerForm" method="post" action="register.php">
                    <div class="form-group">
                        <label for="nom_utilisateur">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Rôle</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="3">Parent</option>
                            <option value="2">Médecin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const eyeIcon = this.querySelector('i');
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});


    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('forgot_password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageElement = document.getElementById('resetPasswordMessage');
        if (data.success) {
            messageElement.textContent = "Un email de réinitialisation a été envoyé à votre adresse.";
            messageElement.className = "alert alert-success";
            $('#forgotPasswordModal').modal('hide');
        } else {
            messageElement.textContent = data.message;
            messageElement.className = "alert alert-danger";
        }
        messageElement.style.display = "block";

        // Faire défiler jusqu'au message
        messageElement.scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        console.error('Error:', error);
        const messageElement = document.getElementById('resetPasswordMessage');
        messageElement.textContent = 'Une erreur s'est produite. Veuillez réessayer.';
        messageElement.className = "alert alert-danger";
        messageElement.style.display = "block";
    });
});
</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<?php include '../views/templates/footer.php'; ?>