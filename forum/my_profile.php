<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Forum.php';
require_once '../functions/Response.php';
require_once 'MongoDB.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$database = new Database();
$db = $database->connect();

$user = new User2($db);
$thread = new Thread($db);
$response = new Response($db);
$mongoClient = new MongoDB();

$currentUser = $user->getUserById($_SESSION['user']['id']);

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $result = false;
        $message = '';
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            switch ($action) {
                case 'update_thread':
                    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
                
                    if ($id && $title && $body) {
                        $result = $thread->updateThread($id, $title, $body);
                        $message = $result ? "Discussion mise à jour avec succès." : "Erreur lors de la mise à jour de la discussion.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour une discussion.";
                    }
                    break;
                    
                case 'update_profile':
                    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
                    
                    if ($username && $email) {
                        $result = $user->updateProfile($_SESSION['user']['id'], $username, $email, $newPassword);
                        if ($result) {
                            $_SESSION['user']['username'] = $username;
                            $_SESSION['user']['email'] = $email;
                        }
                        $message = $result ? "Profil mis à jour avec succès." : "Erreur lors de la mise à jour du profil.";
                    } else {
                        $message = "Le nom d'utilisateur et l'email sont requis.";
                    }
                    break;

                case 'update_response':
                    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                    $body = $_POST['body'];
                    
                    if ($id && $body) {
                        $result = $response->updateResponse($id, $body);
                        $message = $result ? "Commentaire mise à jour avec succès." : "Erreur lors de la mise à jour du commentaire.";
                    } else {
                        $message = "Tous les champs sont requis pour mettre à jour un commentaire.";
                    }
                    break;

                case 'delete_thread':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        // Supprimer le thread de la base de données SQL et MongoDB
                        $result = $thread->deleteThread($id) && $mongoClient->deleteThread($id);
                        $message = $result ? "Discussion supprimée avec succès." : "Erreur lors de la suppression de la discussion.";
                    } else {
                        $message = "ID de la discussion invalide pour la suppression.";
                    }
                    break;

                case 'delete_response':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    
                    if ($id) {
                        $result = $response->deleteResponse($id);
                        $message = $result ? "Réponse supprimée avec succès." : "Erreur lors de la suppression de la réponse.";
                    } else {
                        $message = "ID de réponse invalide pour la suppression.";
                    }
                    break;

                    case 'send_friend_request':
                        $friend_username = filter_input(INPUT_POST, 'friend_username', FILTER_SANITIZE_STRING);
                        if ($friend_username) {
                            // Récupérer l'ID de l'utilisateur à partir du nom d'utilisateur
                            $receiver = $user->getUserByUsername($friend_username);
                            if ($receiver) {
                                $result = $user->sendFriendRequest($_SESSION['user']['id'], $receiver['id']);
                                $message = $result ? "Demande d'ami envoyée avec succès." : "Erreur lors de l'envoi de la demande d'ami.";
                            } else {
                                $message = "Utilisateur non trouvé.";
                            }
                        } else {
                            $message = "Nom d'utilisateur requis pour envoyer une demande d'ami.";
                        }
                        break;
    
                    case 'respond_friend_request':
                        $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                        if ($request_id && in_array($status, ['accepted', 'declined'])) {
                            $result = $user->respondFriendRequest($request_id, $status);
                            $message = $result ? "Demande d'ami mise à jour avec succès." : "Erreur lors de la mise à jour de la demande d'ami.";
                        } else {
                            $message = "ID de la demande et statut requis pour répondre à une demande d'ami.";
                        }
                        break;

                    case 'remove_friend':
                        $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
                        if ($request_id) {
                            $result = $user->removeFriend($request_id);
                            $message = $result ? "Ami supprimé avec succès." : "Erreur lors de la suppression de l'ami.";
                        } else {
                            $message = "ID de la demande d'ami invalide pour la suppression.";
                        }
                        break;

                default:
                    $message = "Action non reconnue.";
            }
        } catch (Exception $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
            error_log($e->getMessage());
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $result ? 'success' : 'danger';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$friendRequests = $user->getFriendRequests($_SESSION['user']['id']);
$friends = $user->getFriends($_SESSION['user']['id']);
$userThreads = $thread->getThreadsByUserId($_SESSION['user']['id']);
$userResponses = $response->getResponsesByUserId($_SESSION['user']['id']);

include_once 'templates/header.php';
include_once 'templates/navbar_forum.php';
?>

<style>
    h1,h2,h3 { text-align: center; }
    body {
        background-image: url('../image/backgroundwebsite.jpg');
        padding-top: 48px;
    }
    h1, .mt-5 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>

<div class="container mt-5">
    <h1>Profil de <?php echo htmlspecialchars($currentUser['nom_utilisateur']); ?></h1>
    
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <form action="my_profile.php" method="POST">
        <input type="hidden" name="action" value="update_profile">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['nom_utilisateur']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
        </div>
        <div class="input-group-append">
            <label for="new_password">Nouveau mot de passe (optionnel)</label>
            <input type="password" class="form-control" id="password" name="new_password">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
        </div>
        <input type="submit" class="btn btn-info" value="Mettre à jour le profil">
    </form>
</div>
<div class="container mt-5">
    <h2>Mes amis</h2>
    <?php if (empty($friends)): ?>
        <p>Vous n'avez pas encore d'amis.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($friends as $friend): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($friend['nom_utilisateur']); ?>
                    <form action="my_profile.php" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet ami ?');">
                        <input type="hidden" name="action" value="remove_friend">
                        <input type="hidden" name="request_id" value="<?php echo $friend['request_id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer de mes amis</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div class="container mt-5">
    <h2>Envoyer une demande d'ami</h2>
    <form action="my_profile.php" method="POST">
        <input type="hidden" name="action" value="send_friend_request">
        <div class="form-group">
            <label for="friend_username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="friend_username" name="friend_username" required>
        </div>
        <button type="submit" class="btn btn-info mt-2">Envoyer la demande</button>
    </form>
</div>
<div class="container mt-5">
    <h2>Demandes d'amis en attente</h2>
    <?php if (empty($friendRequests)): ?>
        <p>Vous n'avez pas de demandes d'amis en attente.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($friendRequests as $request): ?>
                <li class="list-group-item">
                    <span>Demande d'ami de l'utilisateur ID <?php echo $request['sender_id']; ?></span>
                    <form action="my_profile.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="respond_friend_request">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-success">Accepter</button>
                    </form>
                    <form action="my_profile.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="respond_friend_request">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="hidden" name="status" value="declined">
                            <button type="submit" class="btn btn-danger">Refuser</button>
                    </form>
                </li>
                <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>
<div class="container mt-5">
    <h2>Mes threads</h2>
    <?php if (empty($userThreads)): ?>
        <p>Vous n'avez pas encore créé de thread.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($userThreads as $thread): ?>
    <tr>
        <td><?php echo htmlspecialchars($thread['title']); ?></td>
        <td><?php echo $thread['created_at']; ?></td>
        <td>
            <button class="btn btn-warning btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editThreadForm<?php echo $thread['id']; ?>" aria-expanded="false" aria-controls="editThreadForm<?php echo $thread['id']; ?>">
                Modifier
            </button>
            <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce thread ?');">
                <input type="hidden" name="action" value="delete_thread">
                <input type="hidden" name="id" value="<?php echo $thread['id']; ?>">
                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="collapse" id="editThreadForm<?php echo $thread['id']; ?>">
                <form action="my_profile.php" method="POST">
                    <input type="hidden" name="action" value="update_thread">
                    <input type="hidden" name="id" value="<?php echo $thread['id']; ?>">
                    <div class="form-group">
                        <label for="title<?php echo $thread['id']; ?>">Titre</label>
                        <input type="text" class="form-control" id="title<?php echo $thread['id']; ?>" name="title" value="<?php echo htmlspecialchars($thread['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="body<?php echo $thread['id']; ?>">Contenu</label>
                        <textarea class="form-control" id="body<?php echo $thread['id']; ?>" name="body" required><?php echo htmlspecialchars($thread['body']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                </form>
            </div>
        </td>
    </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="add_thread.php" class="btn btn-info">Créer un nouveau thread</a>
</div>

<div class="container mt-5">
    <h2>Mes réponses</h2>
    <?php if (empty($userResponses)): ?>
        <p>Vous n'avez pas encore fait de réponse.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Contenu</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userResponses as $response): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(substr($response['body'], 0, 50)) . '...'; ?></td>
                        <td><?php echo $response['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-modifier" type="button" data-bs-toggle="collapse" data-bs-target="#editResponseForm<?php echo $response['id']; ?>" aria-expanded="false" aria-controls="editResponseForm<?php echo $response['id']; ?>">
                                Modifier
                            </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                        <input type="hidden" name="action" value="delete_response">
                        <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="collapse" id="editResponseForm<?php echo $response['id']; ?>">
                                <form action="my_profile.php" method="POST">
                                <input type="hidden" name="action" value="update_response">
                                <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
                                    <div class="form-group">
                                        <label for="body<?php echo $response['id']; ?>">Contenu</label>
                                        <textarea class="form-control" id="body<?php echo $response['id']; ?>" name="body" required><?php echo htmlspecialchars($response['body'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script>

document.addEventListener('DOMContentLoaded', function() {
    var modifierButtons = document.querySelectorAll('.btn-modifier');
    modifierButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var target = this.getAttribute('data-bs-target');
            var form = document.querySelector(target);
            if (form) {
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                form.classList.toggle('show');
            }
        });
    });
});

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
</script>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<?php include_once 'templates/footer.php'; ?>
