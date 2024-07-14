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

// Requête SQL pour récupérer toutes les colonnes de la table societe_info_competitor
$sql = "SELECT * FROM societe_info_competitor";
$result = $conn->query($sql);

// Créer un tableau pour stocker les résultats
$results = [];

// Vérifier si des résultats ont été trouvés
if ($result->num_rows > 0) {
    // Parcourir chaque ligne de résultat
    while($row = $result->fetch_assoc()) {
        // Appeler l'API OpenCage pour obtenir les informations de géolocalisation
        $geolocation = getGeolocation($row['lat'], $row['long'], $apiKey);
        
        // Stocker le résultat dans le tableau $results
        $formattedAddress = isset($geolocation['results'][0]['formatted']) ? $geolocation['results'][0]['formatted'] : "No results found";
        
        // Ajouter toutes les colonnes de la table au tableau $results
        $results[] = array_merge($row, ["formatted" => $formattedAddress]);
    }
} else {
    echo "0 results";
}

// Fermer la connexion à la base de données
$conn->close();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation Results</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #map, #table {
            height: 600px;
            width: 100%;
        }
        #map {
            display: block;
        }
        #table {
            display: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 10px;
        }
        .dropdown {
            position: relative;
            margin-right: 20px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content button {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
        }
        .dropdown-content button:hover {
            background-color: #f1f1f1;
        }
        .title {
            font-size: 24px;
        }
        /* Custom CSS for the red marker */
        .leaflet-div-icon.custom-red-marker {
            background-color: red;
            border-radius: 50%;
            height: 25px;
            width: 25px;
            border: 2px solid #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="dropdown">
            <button onclick="toggleDropdown()">Menu</button>
            <div id="dropdown-content" class="dropdown-content">
                <button onclick="showMap()">Afficher la carte</button>
                <button onclick="showTable()">Afficher le tableau</button>
            </div>
        </div>
        <div class="title">Magasin concurrent en vente de jeux de sociétés</div>
    </div>
    <h1>Carte des établissements concurrents</h1>
    <div id="map"></div>
    <div id="table">
        <table>
            <thead>
                <tr>
                    <?php
                    // Afficher les en-têtes de colonne
                    foreach ($results[0] as $key => $value) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result) : ?>
                <tr>
                    <?php foreach ($result as $value) : ?>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function toggleDropdown() {
            var dropdownContent = document.getElementById("dropdown-content");
            if (dropdownContent.style.display === "none" || dropdownContent.style.display === "") {
                dropdownContent.style.display = "block";
            } else {
                dropdownContent.style.display = "none";
            }
        }

        function showMap() {
            document.getElementById("map").style.display = "block";
            document.getElementById("table").style.display = "none";
        }

        function showTable() {
            document.getElementById("map").style.display = "none";
            document.getElementById("table").style.display = "block";
        }

        // Initialisation de la carte
        var map = L.map('map').setView([48.897891998291016, 2.0886409282684326], 12);

        // Ajout des tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Ajout des marqueurs pour chaque adresse
        var addresses = <?php echo json_encode($results); ?>;
        addresses.forEach(function(address) {
            L.marker([address.lat, address.long]).addTo(map)
                .bindPopup(address.name + "<br>" + address.formatted + "<br>" + address.site_web + "<br>" + "CA :" + address.ca_dernier + "€" );
        });

        // Ajout d'un marqueur rouge pour les coordonnées spécifiques
        var redIcon = L.icon({
            iconUrl: 'https://www.datavis.fr/tutorials/maps/leaflet-control/map-icons/games.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
        });

        L.marker([48.897891998291016, 2.0886409282684326], {icon: redIcon}).addTo(map)
            .bindPopup("The Gentlemen Pubs");
    </script>
</body>
</html>


