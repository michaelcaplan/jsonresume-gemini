<?php

namespace michaelcaplan\JsonResume\Gemini;

use Laminas\Config\Config;

class Resume
{
    protected array $json;
    protected string $url;
    protected int $ttl = 3600;
    protected int $accessedTime;
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->url = $config->url;
        $this->ttl = $config->ttl ?? 3600;
        $this->config = $config;

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

    public function getSectionNames(): array
    {
        if ($this->isStale()) {
            $this->load();
        }

        $sectionNames = [];

        foreach ($this->json as $sectionName => $section) {
            if ($sectionName == '$schema') {
                continue;
            }

            if (!empty($section)) {
                $sectionNames[] = $sectionName;
            }
        }

        return $sectionNames;
    }

    public function getSection(string $section): array
    {
        if ($this->isStale()) {
            $this->load();
        }

        return $this->json[$section] ?? [];
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
