<?php declare(strict_types=1);

require_once 'CommentsController.php';
require_once 'ResponseRenderer.php';

final class FrontController
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function processRequest(ApiRequest $request): ApiResponse
    {
        if (!$request->hasResourceName()) {
            return new ApiResponse(ApiResponse::STATUS_NOT_FOUND);
        }

        if ('OPTIONS' === $request->getRequestMethod()) {
            // Allow remote AJAX calls
            $headers = [
                "Access-Control-Allow-Origin: *",
                "Content-Type: application/json; charset=UTF-8",
                "Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE",
                "Access-Control-Max-Age: 3600",
                "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
                ];
            return new ApiResponse(ApiResponse::STATUS_OK, $headers);
        }

        switch ($request->getResourceName()) {
            case 'comments':
                $controller = new CommentsController($this->dbConnection);
                $apiResponse = $controller->processRequest($request);
                break;
            default:
                // No supported resource mentioned in URL
                $apiResponse = new ApiResponse(ApiResponse::STATUS_NOT_FOUND);
        }
        return $apiResponse;
    }
}
