<?php

namespace michaelcaplan\JsonResume\Gemini\Server;

use Laminas\Config\Config;
use michaelcaplan\JsonResume\Gemini\Resume;

class Router
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function route(Message $message): void
    {
        $resume = $this->mapHostToResume($message->getRequestUri()->getHost());

        $section = $resume->getSection($this->pathToSection($message->getRequestUri()->getPath()));

        if (!empty($section)) {
            $body = '``` ' . json_encode($section, JSON_PRETTY_PRINT) . ' ```';
        } else {
            $body = '# Nobody home';
        }
        $message->setBody($body);
    }

    protected function mapHostToResume(string $host): Resume
    {
        static $maps = [];

        if (empty($this->config[$host])) {
            $host = 'default';
        }

        if (isset($maps[$host])) {
            return $maps[$host];
        }

        $maps[$host] = new Resume($this->config[$host]->resume);

        return $maps[$host];
    }

    private function pathToSection(string $path): string
    {
        $section = null;

        if (!empty($path)) {
            $section = pathinfo($path, PATHINFO_FILENAME);
        }

        if (empty($section)) {
            $section = 'basics';
        }

        return $section;
    }
}
