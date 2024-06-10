<?php

namespace App\Tests\Behat\Model;

use Symfony\Component\HttpFoundation\Request;

class RequestModel
{
    private string $host = 'localhost';
    private string $scheme = 'http';
    private string $url;
    private string $method;
    private array $headers = [];

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return RequestModel
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return RequestModel
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return RequestModel
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }
    /**
     * @return string
     */
    private function getPreparedUrl(): string
    {
        $path = $this->getUrl();

        if (parse_url($path, PHP_URL_HOST)) {
            return $path;
        }

        return sprintf('%s://%s%s', $this->getScheme(), $this->getHost(), $path);
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    public function setHeader(string $key, $value): self
    {
        if ($this->hasHeader($key)) {
            $this->headers[$key] = $value;

            if (!is_array($this->headers[$key])) {
                $this->headers[$key] = [$this->headers[$key]];
            }

            $this->headers[$key][] = $value;
        } else {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    public function removeHeader(string $key): self
    {
        unset($this->headers[$key]);

        return $this;
    }

    /**
     * @return Request
     */
    public function createRequest(): Request
    {
        $lastRequest = Request::create(
            $this->getPreparedUrl(),
            $this->getMethod()
        );

        $lastRequest->headers->add($this->getHeaders());

        return $lastRequest;
    }
}