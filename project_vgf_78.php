<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx'; // Chemin vers votre fichier Excel

$tableName = isset($_GET['table']) ? $_GET['table'] : '';

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Trouver le tableau par son nom
    $table = null;
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();
        $tableCollection = $sheet->getTableCollection();
        foreach ($tableCollection as $tbl) {
            if ($tbl->getName() === $tableName) {
                $table = $tbl;
                break 2;
            }
        }
    }

    if ($table === null) {
        throw new Exception('Tableau non trouvé.');
    }

    // Récupérer les données du tableau
    $range = $table->getRange();
    $tableData = $sheet->rangeToArray($range, null, true, true, true);

    // Générer du HTML
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Afficher le contenu du tableau</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            .header {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>Contenu du Tableau: ' . htmlspecialchars($tableName) . '</h1>
        <table>';

    // Fonction pour obtenir le style de la cellule
    function getCellStyle($cell) {
        $style = '';
        try {
            $fill = $cell->getStyle()->getFill();
            $color = $fill->getStartColor()->getRGB();
            if ($color) {
                $style .= 'background-color: #' . $color . ';';
            }
        } catch (Exception $e) {
            // Gérer les erreurs liées à la récupération des styles
        }

        try {
            $border = $cell->getStyle()->getBorders();
            $borderStyle = $border->getAllBorders()->getBorderStyle();
            if ($borderStyle) {
                $style .= 'border: ' . $borderStyle . ' 1px solid black;';
            }
        } catch (Exception $e) {
            // Gérer les erreurs liées à la récupération des bordures
        }

        return $style;
    }

    // Fonction pour formater les nombres
    function formatCellValue($cell) {
        $value = $cell->getCalculatedValue(); // Obtenir la valeur calculée
        if (is_string($value)) {
            return htmlspecialchars($value); // Retourner la chaîne telle quelle
        }

        if (is_numeric($value)) {
            // Appliquer le format de nombre
            $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
            if (preg_match('/^\s*[$€]/', $format)) {
                return '€' . number_format($value, 2, ',', ' ');
            } else {
                return number_format($value, 2, ',', ' ');
            }
        }

        return htmlspecialchars($value); // Pour les autres types de valeurs
    }

    // Afficher les noms des colonnes
    $header = true;
    foreach ($tableData as $rowNum => $row) {
        echo '<tr>';
        foreach ($row as $col => $cellValue) {
            $cellCoordinate = $col . $rowNum;
            $cellObject = $sheet->getCell($cellCoordinate);
            $style = getCellStyle($cellObject);

            if ($header) {
                echo '<th style="' . $style . '">' . formatCellValue($cellObject) . '</th>';
            } else {
                echo '<td style="' . $style . '">' . formatCellValue($cellObject) . '</td>';
            }
        }
        echo '</tr>';
        $header = false; // Pour ne pas afficher la première ligne comme en-tête après la première itération
    }

    echo '</table>
    </body>
    </html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
