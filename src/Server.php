<?php

namespace michaelcaplan\JsonResume\Gemini;

use Laminas\Config;
use React\Socket;

class Server
{
    protected Config\Config $config;
    private Socket\SecureServer $server;

    function __construct($configPath)
    {
        $this->config = Config\Factory::fromFile($configPath, true);

        $this->constructServer();
    }

    private function constructServer()
    {
        $this->server = new Socket\SecureServer(
            new Socket\TcpServer($this->config->uri),
            null,
            [
                'allow_self_signed' => true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_3_SERVER,
                'local_cert' => $this->config->cert->path,
                'passphrase' => $this->config->cert->passphrase
            ]
        );

        $this->server->pause();
    }

    public function start()
    {
        $this->server->on('connection', function (Socket\ConnectionInterface $connection) {
            echo 'Secure connection from ' . $connection->getRemoteAddress() . PHP_EOL;

            $connection->on('data', function ($data) use ($connection) {

                echo $data . PHP_EOL;

                $connection->write("20 text/gemini\r\n");
                $connection->write('# Hello ' . rand(0, 1000) . ' World');
                $connection->end();
            });
        });

        $this->server->resume();
    }
}