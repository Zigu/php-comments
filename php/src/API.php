<?php
require_once 'persistence/DatabaseConnector.php';
require_once 'controllers/FrontController.php';
require_once 'controllers/ResponseRenderer.php';
require_once 'controllers/ApiRequest.php';

$dbConnection = (new DatabaseConnector())->getConnection();
$frontController = new FrontController($dbConnection);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$request = new ApiRequest($requestMethod, $uri);

$apiResponse = $frontController->processRequest($request);

ResponseRenderer::render($apiResponse);


