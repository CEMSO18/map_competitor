<?php
require 'C:/xampp/htdocs/map_competitor/vendor/autoload.php';

use OpenCage\Geocoder\Geocoder;

// Récupère la clé API depuis une variable d'environnement
$apiKey = 'efd17e46c9324adfbd1f7d1e04995748'; // Clé API OpenCage

function geocodeAddress($address, $apiKey) {
    $geocoder = new Geocoder($apiKey);
    try {
        $result = $geocoder->geocode($address);
        if ($result && $result['total_results'] > 0) {
            $firstResult = $result['results'][0];
            return [
                'lat' => $firstResult['geometry']['lat'],
                'long' => $firstResult['geometry']['lng'],
                'formatted' => $firstResult['formatted'],
            ];
        } else {
            return null; // Retourne null si aucun résultat trouvé
        }
    } catch (Exception $e) {
        error_log('Error geocoding address: ' . $e->getMessage());
        return null;
    }
}

// Vérifie si le paramètre 'table' est présent dans l'URL
if (isset($_GET['table'])) {
    $table = $_GET['table'];
    
    // Connexion à la base de données MySQL
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "lions_pub_nikita";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $data = [];
    
    // Vérification de la table demandée
    $allowed_tables = ['societe_info_competitor', 'societe_info_vape_competitor'];
    if (in_array($table, $allowed_tables)) {
        $sql = "SELECT * FROM $table";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Assurez-vous que $row['rue'], $row['code_postal'], $row['ville'] contiennent l'adresse à géocoder
                $address = $row['rue'] . ', ' . $row['code_postal'] . ' ' . $row['ville'];
                $geocodedData = geocodeAddress($address, $apiKey);
                if ($geocodedData) {
                    $row['lat'] = $geocodedData['lat'];
                    $row['long'] = $geocodedData['long'];
                    $row['formatted'] = $geocodedData['formatted'];
                }
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Aucune donnée trouvée pour cette table.']);
        }
    } else {
        echo json_encode(['error' => 'Table non autorisée.']);
    }
    
    $conn->close();
} else {
    echo json_encode(['error' => 'Paramètre "table" manquant dans l\'URL.']);
}
?>
