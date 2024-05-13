<?php
namespace App;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class MessageHandler implements MessageComponentInterface
{
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Add the connection to the storage
        $this->connections->attach($conn);

        // Log the new connection
        echo "New connection opened: " . $conn->resourceId . "\n";
        echo "Remote IP address: " . $conn->remoteAddress . "\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection from the storage
        $this->connections->detach($conn);

        // Log the closed connection
        echo "Connection closed: " . $conn->resourceId . "\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        // Log the error
        echo "Error occurred on connection " . $conn->resourceId . ": " . $e->getMessage() . "\n";

        // Close the connection
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                continue;
            }
            $connection->send($msg);
        }
    }
}
