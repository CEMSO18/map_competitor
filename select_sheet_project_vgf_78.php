<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx'; // Chemin vers votre fichier Excel

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);
    $sheetNames = $spreadsheet->getSheetNames();

    // Générer du HTML pour sélectionner une feuille
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choisir une feuille Excel</title>
    </head>
    <body>
        <h1>Choisir une feuille Excel</h1>
        <form action="project_vgf_78.php" method="GET">
            <label for="sheet">Sélectionnez une feuille:</label>
            <select name="sheet" id="sheet">';
    
    // Afficher les noms des feuilles dans un menu déroulant
    foreach ($sheetNames as $sheetIndex => $sheetName) {
        echo '<option value="' . $sheetIndex . '">' . htmlspecialchars($sheetName) . '</option>';
    }

    echo '</select>
            <input type="submit" value="Afficher">
        </form>
    </body>
    </html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
