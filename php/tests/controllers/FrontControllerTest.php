<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FrontControllerTest extends TestCase
{
    public function testMissingUriPart(): void
    {
        $dbConnection = null;

        $request = new ApiRequest('GET', '/');

        $frontController = new FrontController($dbConnection);
        $apiResponse = $frontController->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $apiResponse->getStatus());
        $this->assertNull($apiResponse->getBody());
        $this->assertNull($apiResponse->getHeaders());
    }

    public function testInvalidUriPart(): void
    {
        $dbConnection = null;
        $request = new ApiRequest('GET', '/unknown');

        $frontController = new FrontController($dbConnection);
        $apiResponse = $frontController->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $apiResponse->getStatus());
        $this->assertNull($apiResponse->getBody());
        $this->assertNull($apiResponse->getHeaders());
    }

    public function testCommentsUriPart(): void
    {
        $dbConnection = $this->getMockedPDO([]);

        $request = new ApiRequest('GET', '/comments');

        $frontController = new FrontController($dbConnection);
        $apiResponse = $frontController->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_OK, $apiResponse->getStatus());
        $this->assertSame([], $apiResponse->getBody());
        $this->assertNull($apiResponse->getHeaders());
    }

    public function testOptionRequest(): void
    {
        $dbConnection = null;

        $request = new ApiRequest('OPTIONS', '/comments');

        $frontController = new FrontController($dbConnection);
        $apiResponse = $frontController->processRequest($request);

        $expectedHeaders = [
            'Access-Control-Allow-Origin: *',
            'Content-Type: application/json; charset=UTF-8',
            'Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE',
            'Access-Control-Max-Age: 3600',
            'Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
        ];

        $this->assertSame(ApiResponse::STATUS_OK, $apiResponse->getStatus());
        $this->assertSame($expectedHeaders, $apiResponse->getHeaders());
        $this->assertNull($apiResponse->getBody());
    }

    private function getMockedPDO($fetchAllResult): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetchAll')->willReturn($fetchAllResult);

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('query')->willReturn($query);
        return $db;
    }
}
