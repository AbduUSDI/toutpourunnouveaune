<?php
session_start();
require_once '../views/templates/header.php';
require_once '../views/templates/navbar.php';

$score = $_GET['score'];
?>

<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center">Votre score : <?php echo htmlspecialchars($score); ?></h1>
    <hr>
    <br>
    <a href="quizzes.php" class="btn btn-info mt-3">Retour aux quiz</a>
</div>
<footer class="bg-light text-center text-lg-start mt-4 fixed-bottom" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
        <div class="containerr p-4">
            <p>&copy; 2024 Tout pour un nouveau né. Tous droits réservés.</p>
        </div>
    </footer>
    <!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Inclusion de Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <!-- Inclusion de Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Inclusion de AXIOS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="/Portfolio/toutpourunnouveaune/assets/js/scripts.js"></script>
</body>
</html>

