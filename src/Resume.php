<?php

namespace michaelcaplan\JsonResume\Gemini;

class Resume
{
    protected array $json;
    protected string $url;
    protected int $ttl = 3600;
    protected int $accessedTime;

    public function __construct(string $url, int $ttl = 3600)
    {
        $this->url = $url;
        $this->ttl = $ttl;

        $this->load();
    }

    public function load(): void
    {
        $raw = file_get_contents($this->url);

        if (!empty($raw)) {
            $this->json = json_decode($raw, true);
            $this->accessedTime = time();
        }
    }

    public function isStale(): bool
    {
        if ((time() + $this->ttl) < $this->accessedTime) {
            return true;
        }

        return false;
    }

    public function getSection(string $section): array
    {
        if ($this->isStale()) {
            $this->load();
        }

        return $this->json[$section] ?? [];
    }
}
