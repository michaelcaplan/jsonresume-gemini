<?php

use michaelcaplan\JsonResume\Gemini\Server;
use Laminas\Config;
use Laminas\Log;

require __DIR__ . '/vendor/autoload.php';

$logger = new Log\Logger([
    'exceptionhandler' => true,
    'errorhandler' => true
]);

$logger->addWriter(new Log\Writer\Stream('php://output'));

$server = new Server(
    Config\Factory::fromFile(__DIR__ . '/config.ini', true),
    $logger
);

$server->start();
