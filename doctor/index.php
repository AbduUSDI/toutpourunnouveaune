<?php

session_start(); 
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {     
    header('Location: ../login.php');     
    exit; 
}  
 
require_once '../functions/Database.php'; 
require_once '../functions/Forum.php';
require_once '../functions/AvisMedicaux.php';

// Connexion à la base de données MySQL  
$database = new Database(); 
$db = $database->connect(); 

$forum = new Forum($db); 

$avisMedicaux = new AvisMedicaux($db);

// Récupérer les données  
$threads = $forum->getDerniersThreads(); 
$avis = $avisMedicaux->getDerniersAvis();

// Inclure la navigation admin  
include_once 'navbar_doctor.php'; 
include_once '../templates/header.php'; 
?>

<div>
    
</div>