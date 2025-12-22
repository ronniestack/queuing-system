<?php
/**
 * Windows-safe WebSocket Server
 * Run using: php php-sockets.php
 */

define('HOST_NAME', '127.0.0.1'); // DO NOT use localhost on Windows
define('PORT', 2306);

$null = null;

/* =========================
   WebSocket Handler Class
   ========================= */
class SocketHandler {

    public function send($message) {
        global $clientSocketArray;
        foreach ($clientSocketArray as $client) {
            if ($client !== null) {
                @socket_write($client, $message, strlen($message));
            }
        }
    }

    public function unseal($data) {
        $length = ord($data[1]) & 127;

        if ($length == 126) {
            $masks = substr($data, 4, 4);
            $payload = substr($data, 8);
        } elseif ($length == 127) {
            $masks = substr($data, 10, 4);
            $payload = substr($data, 14);
        } else {
            $masks = substr($data, 2, 4);
            $payload = substr($data, 6);
        }

        $decoded = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $decoded .= $payload[$i] ^ $masks[$i % 4];
        }

        return $decoded;
    }

    public function seal($data) {
        $b1 = 0x81; // FIN + TEXT
        $length = strlen($data);

        if ($length <= 125) {
            return pack('CC', $b1, $length) . $data;
        } elseif ($length < 65536) {
            return pack('CCn', $b1, 126, $length) . $data;
        } else {
            return pack('CCNN', $b1, 127, 0, $length) . $data;
        }
    }

    public function handshake($headers, $client) {
        if (!preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $matches)) {
            return;
        }

        $key = trim($matches[1]);
        $accept = base64_encode(pack(
            'H*',
            sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
        ));

        $response  = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Accept: {$accept}\r\n\r\n";

        socket_write($client, $response, strlen($response));
    }
}

/* =========================
   Server Initialization
   ========================= */
$handler = new SocketHandler();

$serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($serverSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($serverSocket, HOST_NAME, PORT);
socket_listen($serverSocket);

echo "WebSocket server running on ws://" . HOST_NAME . ":" . PORT . PHP_EOL;

$clientSocketArray = [$serverSocket];

/* =========================
   Main Event Loop
   ========================= */
while (true) {

    $changedSockets = $clientSocketArray;
    socket_select($changedSockets, $null, $null, 0, 10);

    /* New Connection */
    if (in_array($serverSocket, $changedSockets)) {
        $newClient = socket_accept($serverSocket);
        $clientSocketArray[] = $newClient;

        $headers = socket_read($newClient, 1024);
        $handler->handshake($headers, $newClient);

        socket_getpeername($newClient, $ip);
        echo "Client connected: {$ip}" . PHP_EOL;

        unset($changedSockets[array_search($serverSocket, $changedSockets)]);
    }

    /* Handle Client Messages */
    foreach ($changedSockets as $client) {

        $bytes = @socket_recv($client, $data, 2048, 0);

        // Client disconnected
        if ($bytes === false || $bytes === 0) {
            socket_getpeername($client, $ip);
            socket_close($client);

            $index = array_search($client, $clientSocketArray);
            unset($clientSocketArray[$index]);

            echo "Client disconnected: {$ip}" . PHP_EOL;
            continue;
        }

        // Message received
        if ($bytes > 0) {
            $message = $handler->unseal($data);

            // Broadcast message to all clients
            $handler->send($handler->seal($message));
        }
    }
}

socket_close($serverSocket);
