<?php declare(strict_types=1);

require_once 'CommentsController.php';
require_once 'ResponseRenderer.php';

final class FrontController
{
    private $dbConnection;
    private $renderer;

    public function __construct($dbConnection, $renderer)
    {
        $this->dbConnection = $dbConnection;
        $this->renderer = $renderer;
    }

    public function processRequest($requestMethod, $uriParts): void
    {
        if (!isset($uriParts[1])) {
            // No resource mentioned in URL
            $this->renderer->renderHeader("HTTP/1.1 404 Not Found");
            exit();
        }

        if ('OPTIONS' === $requestMethod) {
            // Allow remote AJAX calls
            $this->renderer->renderHeader("Access-Control-Allow-Origin: *");
            $this->renderer->renderHeader("Content-Type: application/json; charset=UTF-8");
            $this->renderer->renderHeader("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
            $this->renderer->renderHeader("Access-Control-Max-Age: 3600");
            $this->renderer->renderHeader("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
            exit();
        }

        switch ($uriParts[1]) {
            case 'comments':
                $controller = new CommentsController($this->dbConnection, $requestMethod, $uriParts);
                $response = $controller->processRequest();
                $this->renderer->renderHeader($response['header']['status']);
                if ($response['body']) {
                    $this->renderer->renderBody($response['body']);
                }
                break;
            default:
                // No supported resource mentioned in URL
                $this->renderer->renderHeader("HTTP/1.1 404 Not Found");
        }
    }
}
