<?php declare(strict_types=1);

class CommentsController
{
    private $db;
    private $requestMethod;
    private $uriParts;

    public function __construct($db, $requestMethod, $uriParts)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->uriParts = $uriParts;
    }

    public function processRequest(): array
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($this->uriParts[2])) {
                    $response = $this->notFoundResponse();
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'POST':
                $requestBody = file_get_contents('php://input');
                $response = $this->notFoundResponse();
                break;
            case 'PUT':
                $requestBody = file_get_contents('php://input');
                $response = $this->notFoundResponse();
                break;
            case 'DELETE':
                $response = $this->notFoundResponse();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        return $response;
    }

    private function notFoundResponse(): array
    {
        $response['header'] = array('status' => 'HTTP/1.1 404 Not Found');
        $response['body'] = null;
        return $response;
    }

}
