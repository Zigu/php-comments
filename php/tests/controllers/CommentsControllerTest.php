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
        $dbConnection = $this->getFindAllPDO(null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertSame(['error' => 'phpunit'], $response->getBody());
    }

    public function testGetComment(): void
    {
        $row = $this->expectedRow('1');
        $dbConnection = $this->getFindByIdPDO($row);

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI . '/1');

        $response = $controller->processRequest($request);

        $expectedComment = Comment::newInstance(intval($row['id']), $row['text'], $row['author'], $row['created_at'], $row['updated_at']);
        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
    }

    public function testGetCommentWithException(): void
    {
        $dbConnection = $this->getFindByIdPDO(null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI . '/1');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertSame(['error' => 'phpunit'], $response->getBody());
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

    public function testCreateCommentWithException(): void
    {
        $dbConnection = $this->getInsertPDO(0, null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('POST', self::BASE_URI, json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertEquals(['error' => 'phpunit'], $response->getBody());
    }

    public function testCreateCommentWithValidation(): void
    {
        $dbConnection = $this->getInsertPDO(0, null, null);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => '', 'author' => ''];

        $request = new MockApiRequest('POST', self::BASE_URI, json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_BAD_REQUEST, $response->getStatus());
        $this->assertEquals(['fieldErrors' => ['text' => 'Missing text', 'author' => 'Missing author name']], $response->getBody());
    }

    public function testUpdateComment(): void
    {
        $row = $this->expectedRow('1');

        $dbConnection = $this->getUpdatePDO($row);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('PUT', self::BASE_URI.'/1', json_encode($input));

        $response = $controller->processRequest($request);

        $expectedComment = Comment::newInstance(intval($row['id']), $row['text'], $row['author'], $row['created_at'], $row['updated_at']);

        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
    }

    public function testUpdateCommentWithException(): void
    {
        $dbConnection = $this->getUpdatePDO(null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('PUT', self::BASE_URI."/1", json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertEquals(['error' => 'phpunit'], $response->getBody());
    }

    public function testUpdateCommentWithValidation(): void
    {
        $dbConnection = $this->getUpdatePDO(null);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => '', 'author' => ''];

        $request = new MockApiRequest('PUT', self::BASE_URI."/1", json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_BAD_REQUEST, $response->getStatus());
        $this->assertEquals(['fieldErrors' => ['text' => 'Missing text', 'author' => 'Missing author name']], $response->getBody());
    }

    public function testUpdateCommentWithoutId(): void
    {
        $dbConnection = $this->getUpdatePDO( null);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => '', 'author' => ''];

        $request = new MockApiRequest('PUT', self::BASE_URI, json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());
        $this->assertNull($response->getBody());
    }

    public function testUpdateCommentWithoutWrongId(): void
    {
        $dbConnection = $this->getUpdatePDO(null);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => '', 'author' => ''];

        $request = new MockApiRequest('PUT', self::BASE_URI.'/nonsense', json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());
        $this->assertNull($response->getBody());
    }

    public function testDeleteComment(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI.'/1');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NO_CONTENT, $response->getStatus());

        $this->assertNull($response->getBody());
    }

    public function testDeleteCommentWithoutId(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());

        $this->assertNull($response->getBody());
    }

    public function testDeleteCommentWithInvalidId(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI.'/nonsense');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());

        $this->assertNull($response->getBody());
    }

    public function testDeleteCommentWithException(): void
    {
        $dbConnection = $this->getDeletePDO(new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI."/1");

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertEquals(['error' => 'phpunit'], $response->getBody());
    }

    private function getDeletePDO($exception=null): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        if ($exception !== null) {
            $query->method('execute')->willThrowException($exception);
        }


        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return $db;
    }

    private function getUpdatePDO($row, $exception=null): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        if ($exception !== null) {
            $query->method('execute')->willThrowException($exception);
        }

        $query->method('fetch')->willReturn($row);


        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return $db;
    }


    private function getInsertPDO($resultId, $row, $exception=null): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        if ($exception !== null) {
            $query->method('execute')->willThrowException($exception);
        }

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

    private function getFindAllPDO($result, $exception = null): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        if ($exception !== null) {
            $query->method('fetchAll')->willThrowException($exception);
        } else {
            $query->method('fetchAll')->willReturn($result);
        }

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('query')->willReturn($query);
        return $db;
    }

    private function getFindByIdPDO($result, $exception = null): object
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        if ($exception !== null) {
            $query->method('fetch')->willThrowException($exception);
        } else {
            $query->method('fetch')->willReturn($result);
        }

        $db = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $db->method('prepare')->willReturn($query);
        return $db;
    }

    private function expectedRow($id): array
    {
        return ['id' => $id, 'text' => 'phpunit text', 'author' => 'phpunit author', 'created_at' => date(DatabaseConnector::DATE_FORMAT), 'updated_at' => date(DatabaseConnector::DATE_FORMAT)];
    }
}
