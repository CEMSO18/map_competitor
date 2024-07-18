$zookeeper_port = 2181
$timeout = 5

try {
    $tcpclient = New-Object System.Net.Sockets.TcpClient
    $asyncResult = $tcpclient.BeginConnect('NIKITA', $zookeeper_port, $null, $null)
    $waitHandle = $asyncResult.AsyncWaitHandle
    if ($waitHandle.WaitOne($timeout * 1000, $false)) {
        $tcpclient.EndConnect($asyncResult)
        Write-Output "Zookeeper is running on port $zookeeper_port"
        $tcpclient.Close()
    } else {
        Write-Output "Zookeeper is not running on port $zookeeper_port"
    }
} catch {
    Write-Output "Zookeeper is not running on port $zookeeper_port"
}
