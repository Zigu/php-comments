<?php declare(strict_types=1);

final class ApiResponse
{
    public const STATUS_OK = 200;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_METHOD_NOT_ALLOWED = 405;
    public const STATUS_SERVER_ERROR = 500;

    private $status;
    private $body;
    private $headers;

    public function __construct(int $status, Array $headers = null, $body = null)
    {
        $this->status = $status;
        $this->headers =$headers;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return mixed|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array|null
     */
    public function getHeaders(): ?array
    {
        return $this->headers;
    }


}
