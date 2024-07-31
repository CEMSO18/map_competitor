<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelTableRenderer {
    public static function getCellStyle($cell) {
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

    public static function formatCellValue($cell) {
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
}

$inputFileName = 'C:/Users/csoquet.NIKITA0/Documents/GitHub/map_competitor/document/projet_vapo_game_food_78.xlsx'; // Chemin vers votre fichier Excel

try {
    // Charger le fichier Excel
    $spreadsheet = IOFactory::load($inputFileName);
    
    // Générer du HTML
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Afficher le contenu des tableaux Excel</title>
        <style>
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
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>Contenu des Tableaux du Fichier Excel</h1>';
    
    // Parcourir toutes les feuilles
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Parcourir tous les tableaux de la feuille
        foreach ($sheet->getTableCollection() as $table) {
            $range = $table->getRange();
            $tableData = $sheet->rangeToArray($range, null, true, true, true);

            echo '<h2>' . htmlspecialchars($table->getName()) . '</h2>';
            echo '<table>';

            // Afficher les noms des colonnes
            $header = true;
            foreach ($tableData as $rowNum => $row) {
                echo '<tr>';
                foreach ($row as $col => $cellValue) {
                    $cellCoordinate = $col . $rowNum;
                    $cellObject = $sheet->getCell($cellCoordinate);
                    $style = ExcelTableRenderer::getCellStyle($cellObject);

                    if ($header) {
                        echo '<th style="' . $style . '">' . ExcelTableRenderer::formatCellValue($cellObject) . '</th>';
                    } else {
                        echo '<td style="' . $style . '">' . ExcelTableRenderer::formatCellValue($cellObject) . '</td>';
                    }
                }
                echo '</tr>';
                $header = false; // Pour ne pas afficher la première ligne comme en-tête après la première itération
            }

            echo '</table>';
        }
    }

    echo '</body></html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
