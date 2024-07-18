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

function fetchDataFromTable($table, $apiKey, $conn) {
    $data = [];
    // Sélectionnez uniquement les colonnes nécessaires
    $sql = "SELECT code_ape, name, rue, code_postal, ville, long, lat, ca_dernier, ca_annee FROM $table";
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
    }
    
    return $data;
}

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "lions_pub_nikita";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$results = fetchDataFromTable('societe_info_vape_competitor', $apiKey, $conn);

if (!empty($results)) {
    echo json_encode(['success' => true, 'data' => $results]);
} else {
    echo json_encode(['success' => false, 'error' => 'Aucune donnée trouvée pour la table spécifiée.']);
}

$conn->close();
?>
