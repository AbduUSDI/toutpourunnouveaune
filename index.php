<?php
session_start();
require_once 'functions/Database.php';   




require_once 'templates/header.php';
require_once 'templates/navbar.php'; ?>

<style>
  body {
    background-image: url('image/backgroundwebsite.jpg');
  }
  h1,h2,h3 {
    text-align: center;
}
</style>

<div id="imgtop" class="container mt-5">
<img src="image/Fondimagenouveaune.jpg" class="img-fluid" alt="fondimagetop">
</div>

<div class="container mt-5">
    <h1 class="text-center">Bienvenue sur tout-pour-un-nouveau-ne.com</h1>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <h2>Espace Parents</h2>
            <p>Découvrez nos ressources pour les nouveaux parents.</p>
            <a href="/parent" class="btn btn-primary">Accéder</a>
        </div>
        <div class="col-md-4">
            <h2>Forum</h2>
            <p>Échangez avec d'autres parents et experts.</p>
            <a href="/forum" class="btn btn-primary">Participer</a>
        </div>
        <div class="col-md-4">
            <h2>Recettes pour bébé</h2>
            <p>Des idées de repas adaptés à chaque âge.</p>
            <a href="/recipes" class="btn btn-primary">Découvrir</a>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
