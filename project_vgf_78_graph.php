<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            function generateChart(id, labels, data, title) {
                new Chart(document.getElementById(id), {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data,
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        </script>
    </head>
    <body>
        <h1>Graphiques Excel</h1>';

    // Parcourir toutes les feuilles
    $chartId = 0;
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();

        // Vérifier s'il y a des graphiques dans la feuille
        if ($sheet->getChartCollection()->count() > 0) {
            foreach ($sheet->getChartCollection() as $chart) {
                $chartId++;
                $title = $chart->getTitle()->getCaption();
                $labels = [];
                $data = [];

                // Extraire les données du graphique
                foreach ($chart->getPlotArea()->getPlotGroup() as $plotGroup) {
                    foreach ($plotGroup->getPlotCategoryValues() as $category) {
                        $labels[] = $category->getLabel();
                    }
                    foreach ($plotGroup->getPlotValues() as $value) {
                        $data[] = $value->getLabel();
                    }
                }

                // Afficher le graphique
                echo '<h2>' . htmlspecialchars($title) . '</h2>';
                echo '<canvas id="chart' . $chartId . '" width="400" height="200"></canvas>';
                echo '<script>generateChart("chart' . $chartId . '", ' . json_encode($labels) . ', ' . json_encode($data) . ', "' . htmlspecialchars($title) . '");</script>';
            }
        }
    }

    echo '</body></html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}
?>
