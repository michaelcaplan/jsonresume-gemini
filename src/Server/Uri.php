<?php

namespace michaelcaplan\JsonResume\Gemini\Server;

class Uri
{
    protected array $parts = [];

    public function __construct(string $rawUri = null)
    {
        if (!empty($rawUri)) {
            $this->parts = parse_url($rawUri);
        }
    }

    public function getHost(): string
    {
        return $this->parts['host'] ?? 'default';
    }

    public function getPath(): string
    {
        return $this->parts['path'] ?? '';
    }

    public function __toString(): string
    {
        $uri = isset($this->parts['scheme']) ? $this->parts['scheme'] . '://' : '';
        $uri .= $this->parts['host'] ?? '';
        $uri .= isset($this->parts['port']) ? ':' . $this->parts['port'] : '';
        $uri .= $user = $this->parts['user'] ?? '';
        $password = isset($this->parts['pass']) ? ':' . $this->parts['pass']  : '';
        if ($password) {
            $uri .= ':' . urlencode($password);
        }
        $uri .= ($user || $password) ? '@' : '';
        $uri .= $this->parts['path'] ?? '';
        $uri .= isset($this->parts['query']) ? '?' . $this->parts['query'] : '';
        $uri .= isset($this->parts['fragment']) ? '#' . $this->parts['fragment'] : '';

        return $uri;
    }
}
