<?php

namespace michaelcaplan\JsonResume\Gemini\Server;

use Laminas\Log\Logger;
use michaelcaplan\JsonResume\Gemini\Server;
use React\Socket;

class TcpHandler
{
    private Server $server;
    private Router $router;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->router = new Router($this->server->getConfig());
    }

    public function onConnection(Socket\ConnectionInterface $connection): void
    {
        $connection->on('data', function ($data) use ($connection) {
            $this->onData($connection, $data);
        });
    }

    public function onData(Socket\ConnectionInterface $connection, string $data): void
    {
        $message = new Message(rtrim($data), $connection->getRemoteAddress());

        $this->router->route($message);

        $this->server->getLogger()->log(
            Logger::INFO,
            'Connection from ' . $message->getRemoteUri() . ' requesting ' . $message->getRequestUri()
        );

        $connection->write($message->getResponceHeader());
        $connection->write($message->getBody());
        $connection->end();
    }
}
