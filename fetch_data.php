<?php
// Clé API OpenCage
$apiKey = 'your-opencage-api-key'; // Remplacez par votre clé API OpenCage

// Connexion à la base de données MySQL
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "lions_pub_nikita";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour obtenir des informations de géolocalisation depuis OpenCage
function getGeolocation($lat, $long, $apiKey) {
    $url = "https://api.opencagedata.com/geocode/v1/json?q={$lat}+{$long}&key={$apiKey}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Fonction pour récupérer les données de la table et ajouter l'adresse formatée
function fetchData($table, $conn, $apiKey) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);
    $results = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $geolocation = getGeolocation($row['lat'], $row['long'], $apiKey);
            $formattedAddress = isset($geolocation['results'][0]['formatted']) ? $geolocation['results'][0]['formatted'] : "No results found";
            $results[] = array_merge($row, ["formatted" => $formattedAddress]);
        }
    }
    return $results;
}

$competitorData = fetchData("societe_info_competitor", $conn, $apiKey);
$vapeData = fetchData("societe_info_vape_competitor", $conn, $apiKey);

$conn->close();

echo json_encode(["competitorData" => $competitorData, "vapeData" => $vapeData]);
?>
