<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CommentsControllerTest extends TestCase
{
    private const BASE_URI = '/comments';

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
        $this->assertSame($comments, $response->getBody());
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
        $row = $this->expectedRow('1');
        $pdo = $this->getFindByIdPDO($row);

        $controller = new CommentsController($pdo['dbConnection']);

        $request = new ApiRequest('GET', self::BASE_URI . '/1');

        $response = $controller->processRequest($request);

        $expectedComment = Comment::newInstance(intval($row['id']), $row['text'], $row['author'], $row['created_at'], $row['updated_at']);
        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
    }

    public function testCreateComment(): void
    {
        $row = $this->expectedRow('1');

        $dbConnection = $this->getInsertPDO(1, $row);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('POST', self::BASE_URI, json_encode($input));

        $response = $controller->processRequest($request);

        $expectedComment = Comment::newInstance(intval($row['id']), $row['text'], $row['author'], $row['created_at'], $row['updated_at']);

        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
    }

    private function getInsertPDO($resultId, $row): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetch')->willReturn($row);

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        $db->method('lastInsertId')->willReturn(strval($resultId));
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

    private function getFindByIdPDO($result): array
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetch')->willReturn($result);

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return ['dbConnection' => $db, 'query' => $query];
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

    private function expectedRow($id): array
    {
        return ['id' => $id, 'text' => 'phpunit text', 'author' => 'phpunit author', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
    }
}
