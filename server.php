<?php

require __DIR__ . '/vendor/autoload.php';

$server = new \michaelcaplan\JsonResume\Gemini\Server(__DIR__ . '/config.ini');

$server->start();