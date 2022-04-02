<?php declare(strict_types=1);

final class ResponseRenderer
{
    private function __construct()
    {
        // this utils class only provides static methods.
    }
    public static function render(ApiResponse $response): void
    {
        self::renderStatus($response->getStatus());
        if ($response->getHeaders !== null)
        {
            foreach($response->getHeaders() as $header)
            {
                self::renderHeader($header);
            }
        }

        if ($response->getBody() !== null)
        {
            self::renderBody($response->getBody());
        }
    }

    private static function renderStatus($status): void
    {
        http_response_code($status);
    }
    private static function renderHeader($value): void
    {
        header($value);
    }

    private static function renderBody($value): void
    {
        echo json_encode($value);
    }
}
