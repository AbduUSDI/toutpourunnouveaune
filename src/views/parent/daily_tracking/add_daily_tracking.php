<?php
include '../../../config/Database.php';
include '../../models/TrackingModel.php';

$database = new Database();
$db = $database->connect();

$tracking = new Tracking($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utilisateur_id = filter_input(INPUT_POST, 'utilisateur_id', FILTER_SANITIZE_NUMBER_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $heure_repas = filter_input(INPUT_POST, 'heure_repas', FILTER_SANITIZE_STRING);
    $duree_repas = filter_input(INPUT_POST, 'duree_repas', FILTER_SANITIZE_NUMBER_INT);
    $heure_change = filter_input(INPUT_POST, 'heure_change', FILTER_SANITIZE_STRING);
    $medicament = filter_input(INPUT_POST, 'medicament', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    if ($utilisateur_id && $date && $heure_repas) {
        $result = $tracking->create($utilisateur_id, $date, $heure_repas, $duree_repas, $heure_change, $medicament, $notes);

        if ($result) {
            echo "Suivi quotidien ajouté avec succès.";
        } else {
            echo "Une erreur s'est produite lors de l'ajout du suivi quotidien.";
        }
    } else {
        echo "Les champs utilisateur, date et heure de repas sont requis.";
    }
}
