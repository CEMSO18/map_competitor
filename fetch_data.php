<?php
require 'C:/xampp/htdocs/map_competitor/vendor/autoload.php';

use OpenCage\Geocoder\Geocoder;

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
            return null;
        }
    } catch (Exception $e) {
        error_log('Error geocoding address: ' . $e->getMessage());
        return null;
    }
}

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "lions_pub_nikita";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $data = [];
    
    $allowed_tables = ['societe_info_competitor', 'societe_info_vape_competitor'];
    if (in_array($table, $allowed_tables)) {
        $sql = "SELECT * FROM $table";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $address = $row['rue'] . ', ' . $row['code_postal'] . ' ' . $row['ville'];
                $geocodedData = geocodeAddress($address, $apiKey);
                if ($geocodedData) {
                    $row['lat'] = $geocodedData['lat'];
                    $row['long'] = $geocodedData['long'];
                    $row['formatted'] = $geocodedData['formatted'];
                }
                $data[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Aucune donnée trouvée pour cette table.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Table non autorisée.']);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Paramètre "table" manquant dans l\'URL.']);
}
?>
