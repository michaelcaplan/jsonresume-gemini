<?php

namespace michaelcaplan\JsonResume\Gemini\Server;

class Message
{
    protected Uri $requestUri;
    protected Uri $remoteUri;
    private string $body;
    private int $code = 20;
    private string $mimeType = 'text/gemini';

    public function __construct(string $requestUri, string $remoteUri = null)
    {
        $this->requestUri = new Uri($requestUri);
        $this->remoteUri = new Uri($remoteUri);
    }

    /**
     * @return Uri
     */
    public function getRequestUri(): Uri
    {
        return $this->requestUri;
    }

    /**
     * @return Uri
     */
    public function getRemoteUri(): Uri
    {
        return $this->remoteUri;
    }

    public function setBody(string $body): Message
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setCode(int $code): Message
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setMimeType(string $mimeType): Message
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getResponceHeader(): string
    {
        return $this->getCode() . ' ' . $this->getMimeType() . "\r\n";
    }
}
