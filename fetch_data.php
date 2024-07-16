<?php
require 'C:/xampp/htdocs/map_competitor/vendor/autoload.php';

use OpenCage\Geocoder\Geocoder;

function geocodeAddress($address) {
    $geocoder = new Geocoder('efd17e46c9324adfbd1f7d1e04995748'); // Remplacez par votre clé API OpenCage
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

$tables = ['societe_info_competitor', 'societe_info_vape_competitor'];

foreach ($tables as $table) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $geocodedData = geocodeAddress($row['address']);
            if ($geocodedData) {
                $row['lat'] = $geocodedData['lat'];
                $row['long'] = $geocodedData['long'];
                $row['formatted'] = $geocodedData['formatted'];
            }
            $data[$table][] = $row;
        }
    }
}

echo json_encode($data);

$conn->close();
?>
