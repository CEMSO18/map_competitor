# Vérifiez si Kafka est en cours d'exécution en essayant de se connecter au port Kafka (par défaut 9092)
function Test-Kafka {
    param (
        [string]$server = "NIKITA",
        [int]$port = 9092
    )

    try {
        $tcpConnection = New-Object System.Net.Sockets.TcpClient($server, $port)
        $tcpConnection.Close()
        return $true
    }
    catch {
        return $false
    }
}

# Exécute la fonction et affiche le résultat
if (Test-Kafka) {
    Write-Host "Kafka est actif" -ForegroundColor Green
} else {
    Write-Host "Kafka n'est pas actif" -ForegroundColor Red
}
