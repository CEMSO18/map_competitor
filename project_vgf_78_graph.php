<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx';

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);

    // Sélectionner la feuille contenant les données pour le graphique
    $sheet = $spreadsheet->getSheetByName('Sheet1');
    $data = $sheet->toArray(null, true, true, true);

    // Extraire les données du graphique (adaptez cela à votre structure de données)
    $categories = [];
    $values = [];
    foreach ($data as $row) {
        if (is_numeric($row['A']) && is_numeric($row['B'])) {
            $categories[] = $row['A'];
            $values[] = $row['B'];
        }
    }

    // Générer le graphique avec jpgraph
    $graph = new Graph\Graph(700, 500);
    $graph->SetScale('textlin');

    $barplot = new Plot\BarPlot($values);
    $graph->Add($barplot);

    $graph->title->Set('Graphique des données');
    $graph->xaxis->title->Set('Catégories');
    $graph->yaxis->title->Set('Valeurs');
    $graph->xaxis->SetTickLabels($categories);

    // Sauvegarder le graphique en tant qu'image
    $imagePath = 'graph.png';
    $graph->Stroke($imagePath);

    // Générer le HTML pour afficher l'image
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Graphique Excel</title>
    </head>
    <body>
        <h1>Graphique Excel</h1>
        <img src="' . $imagePath . '" alt="Graphique Excel">
    </body>
    </html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
