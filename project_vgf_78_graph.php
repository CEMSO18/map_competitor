<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

// Chemin vers votre fichier Excel
$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx';

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);

    // Générer le HTML
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Graphiques Excel</title>
        <style>
            body {
                background-color: white;
                color: black;
            }
            .dark-theme {
                background-color: #1a395c;
                color: white;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            .header {
                background-color: #007BFF;
                color: white;
                font-weight: bold;
            }
            .odd-row {
                background-color: white;
            }
            .even-row {
                background-color: #f2f2f2;
            }
        </style>
        <script>
            // Vérifie le thème stocké et applique le thème correspondant
            document.addEventListener("DOMContentLoaded", () => {
                const darkTheme = localStorage.getItem("dark-theme") === "true";
                document.body.classList.toggle("dark-theme", darkTheme);
            });

            // Écouter les modifications de localStorage pour détecter les changements de thème
            window.addEventListener("storage", (event) => {
                if (event.key === "dark-theme") {
                    const darkTheme = event.newValue === "true";
                    document.body.classList.toggle("dark-theme", darkTheme);
                }
            });
        </script>
    </head>
    <body>
        <h1>Graphiques Excel</h1>';

    // Parcourir toutes les feuilles
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();

        // Parcourir tous les graphiques de la feuille
        foreach ($sheet->getChartCollection() as $chart) {
            // Récupérer les données du graphique
            $series = $chart->getPlotArea()->getPlotGroup()[0]->getPlotSeries();
            $categories = $series[0]->getPlotCategoryValues();
            $values = $series[0]->getPlotValues();

            // Générer le graphique avec jpgraph
            $graph = new Graph\Graph(700, 500);
            $graph->SetScale('textlin');

            $barplot = new Plot\BarPlot($values);
            $graph->Add($barplot);

            $graph->title->Set($chart->getTitle()->getCaption());
            $graph->xaxis->title->Set('Catégories');
            $graph->yaxis->title->Set('Valeurs');
            $graph->xaxis->SetTickLabels($categories);

            // Sauvegarder le graphique en tant qu'image
            $imagePath = 'graph_' . $sheetName . '.png';
            $graph->Stroke($imagePath);

            // Afficher le graphique
            echo '<h2>' . htmlspecialchars($chart->getTitle()->getCaption()) . '</h2>';
            echo '<img src="' . $imagePath . '" alt="Graphique Excel">';
        }
    }

    echo '</body></html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
