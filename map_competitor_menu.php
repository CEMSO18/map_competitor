<?php
// Clé API OpenCage
$apiKey = 'efd17e46c9324adfbd1f7d1e04995748';

// Liste des adresses
$addresses = [
    ["lat" => 48.8493, "long" => 2.33, "name" => "VARIANTES"],
    ["lat" => 48.9482, "long" => 2.1917, "name" => "LE GOBELIN"],
    ["lat" => 48.8493, "long" => 2.33, "name" => "LA PLANETE DESSIN"],
    ["lat" => 48.892, "long" => 2.2067, "name" => "MONSIEUR YANIR ZENOU"],
    ["lat" => 48.8412, "long" => 2.3003, "name" => "UCHRONIES GAMES"],
    ["lat" => 48.8846, "long" => 2.2697, "name" => "JUDAIC STORE"],
    ["lat" => 48.9216, "long" => 2.1926, "name" => "LA PLUME D'OR ET D'ARGENT"],
    ["lat" => 48.8057, "long" => 2.1886, "name" => "PUBIZ GAMES"],
    ["lat" => 48.929, "long" => 2.0495, "name" => "TOFOPOLIS"],
    ["lat" => 48.9266843, "long" => 2.2944939, "name" => "MADAME SANDRINE MAURY"],
    ["lat" => 48.8898, "long" => 2.1586, "name" => "CLEMOD"],
    ["lat" => 48.9194, "long" => 2.2748, "name" => "REUSSITE CRITIQUE"],
    ["lat" => 48.8412, "long" => 2.3003, "name" => "MONSIEUR GUILLAUME MOULUN"],
    ["lat" => 48.8925, "long" => 2.3444, "name" => "SASU GNIARK"],
    ["lat" => 48.9526, "long" => 2.1452, "name" => "MONSIEUR YOLAN BENJAMIN BOURGADE"],
    ["lat" => 48.8835, "long" => 2.3219, "name" => "LEBARBU"],
    ["lat" => 48.8967, "long" => 2.2567, "name" => "KHADO KOURBEVOIE"]
];

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

// Créer un tableau pour stocker les résultats
$results = [];

foreach ($addresses as $address) {
    $geolocation = getGeolocation($address['lat'], $address['long'], $apiKey);
    $result = [
        "name" => $address['name'],
        "lat" => $address['lat'],
        "long" => $address['long'],
        "formatted" => isset($geolocation['results'][0]['formatted']) ? $geolocation['results'][0]['formatted'] : "No results found"
    ];
    $results[] = $result;
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
    <h1>Geolocation Results</h1>
    <div id="map"></div>
    <div id="table">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Formatted Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['name']); ?></td>
                    <td><?php echo htmlspecialchars($result['lat']); ?></td>
                    <td><?php echo htmlspecialchars($result['long']); ?></td>
                    <td><?php echo htmlspecialchars($result['formatted']); ?></td>
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
        var map = L.map('map').setView([48.8566, 2.3522], 12);

        // Ajout des tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Ajout des marqueurs pour chaque adresse
        var addresses = <?php echo json_encode($results); ?>;
        addresses.forEach(function(address) {
            L.marker([address.lat, address.long]).addTo(map)
                .bindPopup(address.name + "<br>" + address.formatted);
        });
    </script>
</body>
</html>
