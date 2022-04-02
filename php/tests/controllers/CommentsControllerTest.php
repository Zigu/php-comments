<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CommentsControllerTest extends TestCase
{
    private const BASE_URI = 'comments';

    public function testUnknownRequestMethod(): void
    {
        $dbConnection = null;

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('OPTIONS', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_METHOD_NOT_ALLOWED, $response->getStatus());
        $this->assertNull($response->getBody());
    }

    public function testGetComments(): void
    {
        $comments = array();
        $dbConnection = $this->getFindAllPDO($comments);

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
        $this->assertSame($comments ,$response->getBody());
    }

    public function testGetCommentsWithException(): void
    {
        $dbConnection = $this->getFindAllPDOFailingAtFetchAll();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertSame(['error' => 'phpunit'], $response->getBody());
    }

    public function testGetComment(): void
    {
        $comment = new Comment(1, 'phpunit text', 'phpunit author', time(), time());
        $dbConnection = $this->getFindByIdPDO($comment);

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI.'/1');

        $response = $controller->processRequest($request);

        $this->assertSame($comment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
    }

    public function testCreateComment(): void
    {
        $dbConnection = $this->getInsertPDO(1);

        $controller = new CommentsController($dbConnection);

        $request = new MockApiRequest('POST', self::BASE_URI, '');

        $response = $controller->processRequest($request);


        //$this->assertSame($comment ,$response['body']);
        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());
    }

    private function getInsertPDO($resultId): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetch')->willReturn($resultId);

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return $db;
    }

    private function getFindAllPDO($fetchAllResult): object
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

    private function getFindByIdPDO($result): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetchObject')->willReturn($result);

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return $db;
    }

    private function getFindAllPDOFailingAtFetchAll(): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetchAll')->willThrowException(new PDOException("phpunit"));

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
