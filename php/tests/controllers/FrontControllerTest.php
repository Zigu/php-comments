<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FrontControllerTest extends TestCase
{
    const NOT_FOUND_STATUS = 'HTTP/1.1 404 Not Found';

    public function testMissingUriPart(): void
    {
        $renderer = $this->getMockBuilder(ResponseRenderer::class)
            ->setMethods(['renderHeader', 'renderBody'])
            ->getMock();

        $renderer->expects($this->once())->method('renderHeader')->with($this->equalTo(self::NOT_FOUND_STATUS));
        $renderer->expects($this->never())->method('renderBody');

        $dbConnection = null;
        $uri = '/';
        $uriParts = explode('/', $uri);

        $frontController = new FrontController($dbConnection, $renderer);
        $frontController->processRequest('GET', $uriParts);
    }

    public function testInvalidUriPart(): void
    {
        $renderer = $this->getMockBuilder(ResponseRenderer::class)
            ->setMethods(['renderHeader', 'renderBody'])
            ->getMock();

        $renderer->expects($this->once())->method('renderHeader')->with($this->equalTo(self::NOT_FOUND_STATUS));
        $renderer->expects($this->never())->method('renderBody');

        $dbConnection = null;
        $uri = '/unknown';
        $uriParts = explode('/', $uri);

        $frontController = new FrontController($dbConnection, $renderer);
        $frontController->processRequest('GET', $uriParts);
    }

    public function testCommentsUriPart(): void
    {
        $renderer = $this->getMockBuilder(ResponseRenderer::class)
            ->setMethods(['renderHeader', 'renderBody'])
            ->getMock();

        $renderer->expects($this->once())->method('renderHeader')->with($this->equalTo(self::NOT_FOUND_STATUS));
        $renderer->expects($this->never())->method('renderBody');

        $dbConnection = null;
        $uri = '/comments';
        $uriParts = explode('/', $uri);

        $frontController = new FrontController($dbConnection, $renderer);
        $frontController->processRequest('GET', $uriParts);
    }

    public function testOptionRequest(): void
    {
        $renderer = $this->getMockBuilder(ResponseRenderer::class)
            ->setMethods(['renderHeader', 'renderBody'])
            ->getMock();

        $renderer->expects($this->exactly(5))->method('renderHeader')
            ->withConsecutive(
                [$this->equalTo('Access-Control-Allow-Origin: *')],
                [$this->equalTo('Content-Type: application/json; charset=UTF-8')],
                [$this->equalTo('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE')],
                [$this->equalTo('Access-Control-Max-Age: 3600')],
                [$this->equalTo('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With')]);

        $renderer->expects($this->never())->method('renderBody');

        $dbConnection = null;
        $uri = '/comments';
        $uriParts = explode('/', $uri);

        $frontController = new FrontController($dbConnection, $renderer);
        $frontController->processRequest('OPTIONS', $uriParts);
    }
}
