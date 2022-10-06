<?php declare(strict_types=1);

namespace Rvvup\Sdk;

class Response
{
    /** @var int */
    private $statusCode;
    /** @var string */
    private $body;
    /** @var array */
    private $headers;

    /**
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     */
    public function __construct(int $statusCode, string $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
