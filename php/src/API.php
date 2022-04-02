<?php
require_once 'persistence/DatabaseConnector.php';
require_once 'controllers/FrontController.php';
require_once 'controllers/ResponseRenderer.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode( '/', $uri );
$requestMethod = $_SERVER['REQUEST_METHOD'];

$dbConnection = (new DatabaseConnector())->getConnection();
$renderer = new ResponseRenderer();

$frontController = new FrontController($dbConnection, $renderer);
$frontController->processRequest($requestMethod, $uriParts);


