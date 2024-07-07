<?php
session_start();
require_once 'functions/Database.php';   




require_once 'templates/header.php';
require_once 'templates/navbar.php'; 
?>
    <style>
        body {
            background-image: url('image/backgroundwebsite.jpg');
            padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
        }
        h1, .mt-5 {
            background: whitesmoke;
            border-radius: 15px;
        }
        .section img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 100%;
            height: auto;
        }
        .section {
            background: whitesmoke;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .section h2 {
            text-align: center;
        }
    </style>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Tout pour un nouveau né - Site web</h1>
        <p><strong>Bienvenue sur notre site tout-pour-un-nouveau-ne. Ce site a pour but de vous aider à réussir dans votre nouvelle vie de parents. Si la vie est dure depuis que vous êtes devenus papa ou maman, alors ce site va vous aider à maintenir un mode de vie sain et serein.</strong></p>
        <div class="section">
            <h2>Avis Médicaux</h2>
            <img src="path/to/medical_advice_image.jpg" alt="Avis Médicaux">
          pp class="text-center">Consultez les conseils et recommandations de professionnels de santé pour assurer le bien-être de votre bébé et de toute la famille.</p>
            <a href="medicaladvices.php" class="btn btn-info">Consulter les avis médicaux</a>
        </div>

        <div class="section">
            <h2>Quizz</h2>
            <img src="path/to/quizz_image.jpg" alt="Quizz">
            <p class="text-center">Entraînez-vous à devenir un super parent grâce à nos quizz interactifs et informatifs.</p>
            <a href="quizzes.php" class="btn btn-info">Nos quiz</a>
        </div>

        <div class="section">
            <h2>Recettes pour Bébé</h2>
            <img src="path/to/baby_recipes_image.jpg" alt="Recettes pour Bébé">
            <p class="text-center">Accédez à une multitude de recettes saines et délicieuses spécialement conçues pour les tout-petits.</p>
            <a href="recipes.php" class="btn btn-info">Venez voir nos recettes pour bébé</a>
        </div>

        <div class="section">
            <h2>Guides et Conseils</h2>
            <img src="path/to/guides_tips_image.jpg" alt="Guides et Conseils">
            <p class="text-center">Lisez nos guides détaillés et conseils pratiques sur la nutrition, la santé, et le développement de votre enfant.</p>
            <a href="guides.php" class="btn btn-info">Nos guides</a>
        </div>

        <div class="section">
            <h2>Forum</h2>
            <img src="path/to/forum_image.jpg" alt="Forum">
            <p class="text-center">Participez à notre forum communautaire pour échanger avec d'autres parents, poser vos questions et partager vos expériences.</p>
            <a href="forum/indexforum.php" class="btn btn-info">Notre forum</a>
        </div>
    </div>

    
<?php require_once 'templates/footer.php'; ?>