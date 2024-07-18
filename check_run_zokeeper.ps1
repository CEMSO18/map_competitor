# Vérifiez si ZooKeeper est en cours d'exécution en essayant de se connecter au port ZooKeeper (par défaut 2181)
function Test-ZooKeeper {
    param (
        [string]$server = "localhost",
        [int]$port = 2181
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
if (Test-ZooKeeper) {
    Write-Host "ZooKeeper est actif" -ForegroundColor Green
} else {
    Write-Host "ZooKeeper n'est pas actif" -ForegroundColor Red
}
