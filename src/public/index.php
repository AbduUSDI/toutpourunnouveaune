<?php

$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: /Portfolio/toutpourunnouveaune/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require '../../vendor/autoload.php';

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING) ?? 'home';

$pages = [
    'home',
    'add_comment',
    'contact',
    'food_presentations',
    'forgot_password',
    'get_score',
    'guides',
    'index',
    'login',
    'logout',
    'medicaladvices',
    'quiz',
    'quizzes',
    'recipes',
    'register',
    'reset_password',
    'submit_quiz',
    '404'
];

$baseDir = __DIR__;

if (!in_array($page, $pages)) {
    $page = '404'; 
}

$filePath = realpath($baseDir . "/$page.php");

if ($filePath && strpos($filePath, $baseDir) === 0 && file_exists($filePath)) {
    try {
        include $filePath;
    } catch (Exception $e) {
        echo "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Une erreur s'est produite : fichier introuvable.";
}
