<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelTableRenderer {
    public static function getCellStyle($cell) {
        $styleArray = $cell->getStyle();
        $style = '';

        // Couleur de fond
        $fill = $styleArray->getFill()->getStartColor()->getRGB();
        if ($fill != '000000' && $fill != 'FFFFFF') { // Ne pas appliquer le noir ou blanc par défaut
            $style .= 'background-color: #' . $fill . ';';
        } else {
            $style .= 'background-color: #FFFFFF;'; // Assurez-vous que le fond est toujours blanc
        }

        // Bordures
        $borders = $styleArray->getBorders();
        foreach (['top', 'right', 'bottom', 'left'] as $borderPosition) {
            $border = $borders->{'get' . ucfirst($borderPosition)}();
            if ($border->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                $style .= 'border-' . $borderPosition . ': 1px solid #' . $border->getColor()->getRGB() . ';';
            }
        }

        // Alignement
        $alignment = $styleArray->getAlignment();
        $style .= 'text-align: ' . $alignment->getHorizontal() . ';';
        $style .= 'vertical-align: ' . $alignment->getVertical() . ';';

        // Polices
        $font = $styleArray->getFont();
        $style .= 'font-family: ' . $font->getName() . ';';
        $style .= 'font-size: ' . $font->getSize() . 'pt;';
        if ($font->getBold()) {
            $style .= 'font-weight: bold;';
        }
        if ($font->getItalic()) {
            $style .= 'font-style: italic;';
        }
        if ($font->getUnderline() !== \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE) {
            $style .= 'text-decoration: underline;';
        }
        $fontColor = $font->getColor()->getRGB();
        if ($fontColor && $fontColor != '000000') {
            $style .= 'color: #' . $fontColor . ';';
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
        <title>Financial Estimate</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                background-color: #FFFFFF; /* Assurez-vous que le fond est toujours blanc */
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            .header {
                background-color: #007BFF; /* Bleu pour les en-têtes */
                color: white;
                font-weight: bold;
            }
            .odd-row {
                background-color: #FFFFFF; /* Blanc pour les lignes impaires */
            }
            .even-row {
                background-color: #FFFFFF; /* Blanc pour les lignes paires */
            }
        </style>
    </head>
    <body>
        <h1>Présentation des estimation financières</h1>';

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
            $rowIndex = 0;
            foreach ($tableData as $rowNum => $row) {
                $rowClass = $header ? 'header' : ($rowIndex % 2 == 0 ? 'even-row' : 'odd-row');
                echo '<tr class="' . $rowClass . '">';
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
                $rowIndex++;
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
