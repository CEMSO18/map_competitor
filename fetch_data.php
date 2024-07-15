<?php
require 'vendor/autoload.php'; // Assurez-vous d'avoir inclus le chemin correct vers autoload.php de Composer

// Importation de la classe OpenCageGeocode
use OpenCage\Geocoder\Geocoder;

// Fonction pour géocoder une adresse avec OpenCage
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
            return null; // Retourne null si aucune résultat trouvé
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

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupération des données en fonction de la table spécifiée
$table = $_GET['table'];
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);

// Vérification des résultats et formatage en JSON
if ($result->num_rows > 0) {
    $data = array();
    while($row = $result->fetch_assoc()) {
        // Géocodage de l'adresse et ajout des coordonnées géographiques
        $geocodedData = geocodeAddress($row['address']);
        if ($geocodedData) {
            $row['lat'] = $geocodedData['lat'];
            $row['long'] = $geocodedData['long'];
            $row['formatted'] = $geocodedData['formatted'];
            $data[] = $row;
        } else {
            // En cas d'erreur de géocodage, ajoutez l'entrée sans coordonnées
            $data[] = $row;
        }
    }
    echo json_encode($data);
} else {
    echo json_encode(array()); // Retourne un tableau vide si aucune donnée trouvée
}

$conn->close();
?>
