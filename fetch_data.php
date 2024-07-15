<?php
// Clé API OpenCage
$apiKey = 'efd17e46c9324adfbd1f7d1e04995748';

// Connexion à la base de données MySQL
$servername = "127.0.0.1";   // Adresse du serveur MySQL
$username = "root";   // Nom d'utilisateur MySQL (root pour localhost)
$password = "";   // Mot de passe MySQL vide si non défini
$dbname = "lions_pub_nikita";   // Nom de la base de données MySQL

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour obtenir les données d'une table
function getTableData($conn, $table, $apiKey) {
    $sql = "SELECT * FROM " . $conn->real_escape_string($table);
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

// Récupérer les données des deux tables
$data = [
    "societe_info_competitor" => getTableData($conn, "societe_info_competitor", $apiKey),
    "societe_info_vape_competitor" => getTableData($conn, "societe_info_vape_competitor", $apiKey)
];

// Fermer la connexion à la base de données
$conn->close();

// Retourner les résultats au format JSON
echo json_encode($data);
?>
