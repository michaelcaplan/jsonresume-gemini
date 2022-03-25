<?php

namespace michaelcaplan\JsonResume\Gemini;

use Laminas\Config;
use Laminas\Log\Logger;
use michaelcaplan\JsonResume\Gemini\Server\TcpHandler;
use React\Socket;

class Server
{
    protected Config\Config $config;
    private Socket\SecureServer $server;
    private TcpHandler $handler;
    private Logger $logger;

    public function __construct(Config\Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->constructServer();
    }

    private function constructServer(): void
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

        $this->handler = new TcpHandler($this);
        $this->server->on('connection', [$this->handler, 'onConnection']);
    }

    public function start(): void
    {
        $this->server->resume();
    }

    /**
     * @return Config\Config
     */
    public function getConfig(): Config\Config
    {
        return $this->config;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }
}
