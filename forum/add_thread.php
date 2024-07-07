<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'functions/Database.php';
require_once 'functions/Thread.php';

$database = new Database();
$db = $database->connect();

$thread = new Thread($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $user_id = $_SESSION['user']['id'];

    if ($thread->addThread($title, $body, $user_id)) {
        header('Location: indexforum.php');
        exit;
    } else {
        $error_message = "Erreur lors de la création de la discussion. Veuillez réessayer.";
    }
}

require_once 'templates/header.php';
require_once 'templates/navbar_forum.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/backgroundwebsite.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5">
    <h1>Créer une nouvelle discussion</h1>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="add_thread.php">
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="body">Contenu</label>
            <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Créer la discussion</button>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
