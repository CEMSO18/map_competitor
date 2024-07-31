<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx'; // Chemin vers votre fichier Excel

$tableName = isset($_GET['table']) ? $_GET['table'] : '';

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Chercher le tableau par son nom
    $table = null;
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetTables = $sheet->getTableCollection();
        foreach ($sheetTables as $tbl) {
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

    // Évaluer les formules et récupérer les valeurs calculées
    $calculatedData = [];
    foreach ($sheet->getRowIterator() as $row) {
        $rowIndex = $row->getRowIndex();
        foreach ($row->getCellIterator() as $cell) {
            $colIndex = $cell->getColumn();
            $cellValue = $sheet->getCell($colIndex . $rowIndex)->getCalculatedValue();
            $calculatedData[$rowIndex][$colIndex] = $cellValue;
        }
    }

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
        </style>
    </head>
    <body>
        <h1>Contenu du Tableau: ' . htmlspecialchars($tableName) . '</h1>
        <table>';

    // Fonction pour obtenir le style de la cellule (simplifiée)
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

    // Afficher les données du tableau avec mise en forme
    foreach ($tableData as $rowNum => $row) {
        echo '<tr>';
        foreach ($row as $col => $cell) {
            $cellCoordinate = $col . $rowNum;
            $cellObject = $sheet->getCell($cellCoordinate);
            $style = getCellStyle($cellObject);
            $calculatedValue = $calculatedData[$rowNum][$col];
            echo '<td style="' . $style . '">' . htmlspecialchars($calculatedValue) . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>
    </body>
    </html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
