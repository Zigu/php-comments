<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CommentsServiceTest extends TestCase
{
    public function testFindAll(): void
    {
        $query = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $query->method('fetchAll')
            ->willReturn([$this->expectedRow('1')]);

        $query->expects($this->once())
            ->method('fetchAll')
            ->with($this->equalTo(PDO::FETCH_ASSOC));

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('query')->willReturn($query);

        $dbConnection->expects($this->once())
            ->method('query')
            ->with($this->equalTo('SELECT id, text, author, created_at, updated_at FROM comments ORDER BY updated_at DESC;'));

        $service = new CommentsService($dbConnection);

        $comments = $service->findAll();
        $this->assertCount(1, $comments);
        $this->assertEquals(1, $comments[0]->getId());
    }

    public function testFindById(): void
    {
        $preparedStatement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $preparedStatement->method('fetch')
            ->willReturn($this->expectedRow('1'));

        $preparedStatement->expects($this->once())
            ->method('bindParam')
            ->with($this->equalTo('id'), $this->equalTo(1), $this->equalTo(PDO::PARAM_INT));

        $preparedStatement->expects($this->once())
            ->method('execute');

        $preparedStatement->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(PDO::FETCH_ASSOC));

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('prepare')->willReturn($preparedStatement);

        $dbConnection->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT id, text, author, created_at, updated_at FROM comments WHERE id=:id;'));

        $service = new CommentsService($dbConnection);

        $comment = $service->findById(1);
        $this->assertNotNull($comment);
        $this->assertEquals(1, $comment->getId());
    }

    public function testFailingFindById(): void
    {
        $preparedStatement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $preparedStatement->method('fetch')
            ->willReturn(false);

        $preparedStatement->expects($this->once())
            ->method('bindParam')
            ->with($this->equalTo('id'), $this->equalTo(1), $this->equalTo(PDO::PARAM_INT));

        $preparedStatement->expects($this->once())
            ->method('execute');

        $preparedStatement->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(PDO::FETCH_ASSOC));

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('prepare')->willReturn($preparedStatement);

        $dbConnection->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT id, text, author, created_at, updated_at FROM comments WHERE id=:id;'));

        $service = new CommentsService($dbConnection);

        $comment = $service->findById(1);
        $this->assertNull($comment);
    }

    public function testDeleteById(): void
    {
        $preparedStatement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $preparedStatement->expects($this->once())
            ->method('bindParam')
            ->with($this->equalTo('id'), $this->equalTo(1), $this->equalTo(PDO::PARAM_INT));

        $preparedStatement->expects($this->once())
            ->method('execute');

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('prepare')->willReturn($preparedStatement);

        $dbConnection->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('DELETE FROM comments WHERE id=:id;'));

        $service = new CommentsService($dbConnection);

        $service->deleteById(1);
    }

    public function testInsert(): void
    {
        $preparedStatement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $preparedStatement->expects($this->exactly(2))
            ->method('bindParam')
            ->withConsecutive(
                [$this->equalTo('text'), $this->equalTo('phpunit text'), $this->equalTo(PDO::PARAM_STR)],
                [$this->equalTo('author'), $this->equalTo('phpunit author'), $this->equalTo(PDO::PARAM_STR)]
            );

        $preparedStatement->expects($this->once())
            ->method('execute');

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('prepare')->willReturn($preparedStatement);
        $dbConnection->method('lastInsertId')->willReturn('1');

        $dbConnection->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO comments (text, author) VALUES (:text, :author);'));

        $dbConnection->expects($this->once())
            ->method('lastInsertId');

        $service = new CommentsService($dbConnection);

        $input = ['author' => 'phpunit author', 'text' => 'phpunit text'];

        $lastInsertId = $service->insert($input);

        $this->assertEquals('1', $lastInsertId);
    }

    public function testUpdate(): void
    {
        $updateDate = date(DatabaseConnector::DATE_FORMAT);

        $preparedStatement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $preparedStatement->expects($this->exactly(4))
            ->method('bindParam')
            ->withConsecutive(
                [$this->equalTo('text'), $this->equalTo('phpunit text'), $this->equalTo(PDO::PARAM_STR)],
                [$this->equalTo('author'), $this->equalTo('phpunit author'), $this->equalTo(PDO::PARAM_STR)],
                [$this->equalTo('updatedAt'), $this->equalTo($updateDate), $this->equalTo(PDO::PARAM_STR)],
                [$this->equalTo('id'), $this->equalTo(1), $this->equalTo(PDO::PARAM_INT)]
            );

        $preparedStatement->expects($this->once())
            ->method('execute');

        $dbConnection = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $dbConnection->method('prepare')->willReturn($preparedStatement);

        $dbConnection->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('UPDATE comments SET text=:text, author=:author, updated_at=:updatedAt WHERE id=:id;'));

        $service = new CommentsService($dbConnection);

        $input = ['author' => 'phpunit author', 'text' => 'phpunit text'];

        $service->update(1, $input, $updateDate);
    }

    private function expectedRow($id): array
    {
        return ['id' => $id, 'text' => 'phpunit text', 'author' => 'phpunit author', 'created_at' => date(DatabaseConnector::DATE_FORMAT), 'updated_at' => date(DatabaseConnector::DATE_FORMAT)];
    }
}
