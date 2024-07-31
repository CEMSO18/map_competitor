<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx'; // Chemin vers votre fichier Excel

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Récupérer tous les tableaux (nommés) dans la feuille
    $tables = [];
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetTables = $sheet->getTableCollection();
        foreach ($sheetTables as $table) {
            $tables[] = [
                'sheet' => $sheetName,
                'name' => $table->getName()
            ];
        }
    }

    // Générer du HTML pour sélectionner un tableau
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choisir un tableau Excel</title>
    </head>
    <body>
        <h1>Choisir un tableau Excel</h1>
        <form action="project_vgf_78.php" method="GET">
            <label for="table">Sélectionnez un tableau:</label>
            <select name="table" id="table">';
    
    // Afficher les noms des tableaux dans un menu déroulant
    foreach ($tables as $table) {
        echo '<option value="' . htmlspecialchars($table['name']) . '">' . htmlspecialchars($table['sheet'] . ' - ' . $table['name']) . '</option>';
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
