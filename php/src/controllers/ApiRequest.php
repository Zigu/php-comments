<?php

class ApiRequest
{
    private $requestMethod;
    private $uri;
    private $requestBody = null;

    public function __construct(string $requestMethod, string $uri)
    {
        $this->requestMethod = $requestMethod;
        $this->uri = $uri;
    }

    public function hasResourceName(): bool
    {
        return $this->getResourceName() !== '';
    }

    public function getResourceName(): string
    {
        return $this->getUriParts()[1];
    }

    public function getId(): ?string
    {
        $uriParts = $this->getUriParts();
        return sizeof($uriParts) == 3 ? $uriParts[2] : null;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestBody(): string
    {
        if ('POST' === $this->requestMethod || 'PUT' === $this->requestMethod) {
            if ($this->requestBody === null) {
                $this->requestBody = file_get_contents('php://input');
            }
            return $this->requestBody;
        }
        throw new BadFunctionCallException('request method '.$this->requestMethod.' does not support a request body.');
    }

    private function getUriParts(): array
    {
        return explode('/', $this->uri);
    }
}
