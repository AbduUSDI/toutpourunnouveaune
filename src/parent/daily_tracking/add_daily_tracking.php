<?php
include '../../../config/Database.php';
include '../../models/TrackingModel.php';

$database = new Database();
$db = $database->connect();

$tracking = new Tracking($db);

// Vérifier si des données sont soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $utilisateur_id = $_POST['utilisateur_id'];
    $date = $_POST['date'];
    $heure_repas = $_POST['heure_repas'];
    $duree_repas = $_POST['duree_repas'];
    $heure_change = $_POST['heure_change'];
    $medicament = $_POST['medicament'];
    $notes = $_POST['notes'];

    // Ajouter le suivi quotidien
    $result = $tracking->create($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes);

    if ($result) {
        // Succès
        echo "Suivi quotidien ajouté avec succès.";
    } else {
        // Erreur
        echo "Une erreur s'est produite lors de l'ajout du suivi quotidien.";
    }
}