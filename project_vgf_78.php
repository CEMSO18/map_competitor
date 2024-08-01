<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelTableRenderer {
    public static function getCellStyle($cell) {
        $style = '';

        // Bordures
        $borders = $cell->getStyle()->getBorders();
        foreach (['top', 'right', 'bottom', 'left'] as $borderPosition) {
            $border = $borders->{'get' . ucfirst($borderPosition)}();
            if ($border->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                $style .= 'border-' . $borderPosition . ': 1px solid #' . $border->getColor()->getRGB() . ';';
            }
        }

        // Alignement
        $alignment = $cell->getStyle()->getAlignment();
        $style .= 'text-align: ' . $alignment->getHorizontal() . ';';
        $style .= 'vertical-align: ' . $alignment->getVertical() . ';';

        // Polices
        $font = $cell->getStyle()->getFont();
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
        $style .= 'color: #000000;'; // Assurer que le texte est noir

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
            body.light-theme {
                background-color: #ffffff;
                color: #000000;
            }
            body.dark-theme {
                background-color: #121212;
                color: #ffffff;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table, th, td {
                border: 1px solid black; /* Bordures visibles */
            }
            th, td {
                padding: 8px;
                text-align: left;
                background-color: #ffffff; /* Fond blanc pour le mode clair */
                color: #000000; /* Texte noir pour le mode clair */
            }
            .header {
                background-color: #007BFF; /* Bleu pour les en-têtes */
                color: white;
                font-weight: bold;
            }
            .dark-theme table {
                padding: 8px;
                text-align: left;
                background-color: #1a395c; /* Fond pour les tableaux en mode sombre */
                color: #ffffff; /* Texte blanc pour le mode sombre */
            }
            .dark-theme th, .dark-theme td {
                background-color: #1a395c; /* Fond sombre pour les cellules en mode sombre */
                color: #ffffff; /* Texte blanc pour le mode sombre */
            }
        </style>
    </head>
    <body>
        <h1>Présentation des estimations financières</h1>';

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
                $rowClass = $header ? 'header' : '';
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
            }

            echo '</table>';
        }
    }

    echo '
        <script>
            // Vérifie le thème stocké et applique le thème correspondant
            document.addEventListener("DOMContentLoaded", () => {
                const darkTheme = localStorage.getItem("dark-theme") === "true";
                document.body.classList.toggle("dark-theme", darkTheme);
                document.body.classList.toggle("light-theme", !darkTheme);
            });

            // Écouter les modifications de localStorage pour détecter les changements de thème
            window.addEventListener("storage", (event) => {
                if (event.key === "dark-theme") {
                    const darkTheme = event.newValue === "true";
                    document.body.classList.toggle("dark-theme", darkTheme);
                    document.body.classList.toggle("light-theme", !darkTheme);
                }
            });
        </script>
    </body></html>';

} catch (Exception $e) {
    die('Erreur de chargement du fichier "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
        . '": ' . $e->getMessage());
}
?>
