<?php

class MockApiRequest extends ApiRequest
{
    private $requestBody;

    public function __construct(string $requestMethod, string $uri, string $requestBody)
    {
        parent::__construct($requestMethod, $uri);
        $this->requestBody = $requestBody;
    }

    public function getRequestBody(): string
    {
        return $this->requestBody;
    }


}
