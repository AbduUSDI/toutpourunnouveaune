<?php
session_start();
require_once 'functions/Database.php';   

require_once 'templates/header.php';
require_once 'templates/navbar.php'; 
?>
    <style>
        body {
            background-image: url('image/background.jpg');
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
        <h1 class="text-center mb-5">Tout pour un nouveau-né - Site web</h1>
        <p><strong>Bienvenue sur notre site "Tout pour un nouveau-né". Ce site a pour but de vous aider à réussir dans votre nouvelle vie de parents. Si la vie est devenue difficile depuis que vous êtes devenus papa ou maman, alors ce site va vous aider à maintenir un mode de vie sain et serein. Nous vous proposons des ressources variées, allant des conseils médicaux aux recettes pour bébés, en passant par des quizz interactifs et un forum pour échanger avec d'autres parents. Nous espérons que vous trouverez ici tout ce dont vous avez besoin pour traverser cette merveilleuse aventure avec votre bébé.</strong></p>
        
        <div class="section">
            <h2>Avis Médicaux</h2>
            <img src="image/avimedicaux.jpg" alt="Avis Médicaux">
            <p class="text-center">Consultez les conseils et recommandations de professionnels de santé pour assurer le bien-être de votre bébé et de toute la famille. Nos experts partagent leurs connaissances et expériences pour vous aider à comprendre et à gérer les besoins médicaux de votre nouveau-né. Que ce soit pour des conseils sur les vaccinations, les soins de routine ou les petites maladies de l'enfance, nos avis médicaux sont là pour vous guider et vous rassurer.</p>
            <a href="medicaladvices.php" class="btn btn-info">Consulter les avis médicaux</a>
        </div>

        <div class="section">
            <h2>Quizz</h2>
            <img src="image/quiz.jpg" alt="Quizz">
            <p class="text-center">Entraînez-vous à devenir un super parent grâce à nos quizz interactifs et informatifs. Ces quizz sont conçus pour vous aider à tester et à améliorer vos connaissances sur les soins et le développement de votre bébé. Apprenez en vous amusant et découvrez des informations précieuses sur des sujets variés tels que l'alimentation, la santé et les étapes de croissance de votre enfant.</p>
            <a href="quizzes.php" class="btn btn-info">Nos quizz</a>
        </div>

        <div class="section">
            <h2>Recettes pour Bébé</h2>
            <img src="image/recettes.jpg" alt="Recettes pour Bébé">
            <p class="text-center">Accédez à une multitude de recettes saines et délicieuses spécialement conçues pour les tout-petits. Nos recettes sont élaborées par des nutritionnistes pour garantir que chaque repas est équilibré et adapté aux besoins nutritionnels de votre bébé. Que vous cherchiez des idées pour diversifier l'alimentation de votre enfant ou des solutions pour les petits mangeurs, nos recettes pour bébés sont là pour vous inspirer.</p>
            <a href="recipes.php" class="btn btn-info">Venez voir nos recettes pour bébé</a>
        </div>

        <div class="section">
            <h2>Guides et Conseils</h2>
            <img src="image/guides.jpg" alt="Guides et Conseils">
            <p class="text-center">Lisez nos guides détaillés et conseils pratiques sur la nutrition, la santé, et le développement de votre enfant. Nous couvrons une large gamme de sujets pour vous aider à naviguer les défis de la parentalité. Que vous soyez à la recherche de conseils pour les nuits sans sommeil, des astuces pour apaiser les pleurs de bébé, ou des informations sur le développement psychomoteur, nos guides sont une ressource précieuse pour tous les parents.</p>
            <a href="guides.php" class="btn btn-info">Nos guides</a>
        </div>

        <div class="section">
            <h2>Forum</h2>
            <img src="image/forum.jpg" alt="Forum">
            <p class="text-center">Participez à notre forum communautaire pour échanger avec d'autres parents, poser vos questions et partager vos expériences. Notre forum est un espace convivial où vous pouvez trouver du soutien, des conseils et des amitiés avec d'autres parents qui vivent des situations similaires. Rejoignez notre communauté et bénéficiez de l'expérience collective pour rendre votre parcours de parent plus facile et plus agréable.</p>
            <a href="forum/indexforum.php" class="btn btn-info">Notre forum</a>
        </div>
    </div>
<?php require_once 'templates/footer.php'; ?>
