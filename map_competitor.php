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

// Parcourir chaque adresse et obtenir les informations de géolocalisation
foreach ($addresses as $address) {
    $geolocation = getGeolocation($address['lat'], $address['long'], $apiKey);
    echo "Name: " . $address['name'] . "\n";
    echo "Latitude: " . $address['lat'] . "\n";
    echo "Longitude: " . $address['long'] . "\n";
    if (isset($geolocation['results'][0]['formatted'])) {
        echo "Formatted Address: " . $geolocation['results'][0]['formatted'] . "\n";
    } else {
        echo "No results found for this location.\n";
    }
    echo "-------------------------------------\n";
}
?>
