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
        $this->assertEquals($comments, $response->getBody());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
    }

    public function testGetCommentsWithException(): void
    {
        $dbConnection = $this->getFindAllPDO(null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertSame(['error' => 'phpunit'], $response->getBody());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
    }

    public function testGetComment(): void
    {
        $expectedComment = $this->expectedComment(1);
        $dbConnection = $this->getFindByIdPDO($expectedComment);

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI . '/1');

        $response = $controller->processRequest($request);

        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
    }

    public function testGetCommentWithException(): void
    {
        $dbConnection = $this->getFindByIdPDO(null, new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('GET', self::BASE_URI . '/1');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertSame(['error' => 'phpunit'], $response->getBody());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
    }

    public function testCreateComment(): void
    {
        $expectedComment = $this->expectedComment(1);

        $dbConnection = $this->getInsertPDO(1, $expectedComment);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('POST', self::BASE_URI, json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
    }

    public function testUpdateComment(): void
    {
        $expectedComment = $this->expectedComment(1);

        $dbConnection = $this->getUpdatePDO($expectedComment);

        $controller = new CommentsController($dbConnection);

        $input = ['text' => 'phpunit text', 'author' => 'phpunit author'];

        $request = new MockApiRequest('PUT', self::BASE_URI.'/1', json_encode($input));

        $response = $controller->processRequest($request);

        $this->assertEquals($expectedComment, $response->getBody());
        $this->assertSame(ApiResponse::STATUS_OK, $response->getStatus());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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
        $this->assertNull($response->getHeaders());
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
        $this->assertNull($response->getHeaders());
    }

    public function testDeleteComment(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI.'/1');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NO_CONTENT, $response->getStatus());
        $this->assertNull($response->getBody());
        $this->assertNull($response->getHeaders());
    }

    public function testDeleteCommentWithoutId(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI);

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());
        $this->assertNull($response->getBody());
        $this->assertNull($response->getHeaders());
    }

    public function testDeleteCommentWithInvalidId(): void
    {
        $dbConnection = $this->getDeletePDO();

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI.'/nonsense');

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_NOT_FOUND, $response->getStatus());
        $this->assertNull($response->getBody());
        $this->assertNull($response->getHeaders());
    }

    public function testDeleteCommentWithException(): void
    {
        $dbConnection = $this->getDeletePDO(new PDOException("phpunit"));

        $controller = new CommentsController($dbConnection);

        $request = new ApiRequest('DELETE', self::BASE_URI."/1");

        $response = $controller->processRequest($request);

        $this->assertSame(ApiResponse::STATUS_SERVER_ERROR, $response->getStatus());
        $this->assertEquals(['error' => 'phpunit'], $response->getBody());
        $this->assertEquals([ApiResponse::HEADER_CONTENT_TYPE_JSON], $response->getHeaders());
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

    private function getUpdatePDO($result, $exception=null): object
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


    private function getInsertPDO($resultId, $comment, $exception=null): object
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

        $query->method('fetchObject')->willReturn($comment);


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
            $query->method('fetchObject')->willThrowException($exception);
        } else {
            $query->method('fetchObject')->willReturn($result);
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

    private function expectedComment($id): object
    {
        return Comment::newInstance($id, 'phpunit text', 'phpunit author', date(DatabaseConnector::DATE_FORMAT), date(DatabaseConnector::DATE_FORMAT));
    }
}
