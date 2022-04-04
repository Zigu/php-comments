<?php declare(strict_types=1);

require __DIR__.'/../model/Comment.php';

final class CommentsService
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }


    public function findAll(): array
    {
        $statement = "SELECT id, text, author, created_at AS createdAt, updated_at AS updatedAt FROM comments ORDER BY updated_at DESC;";
        $query = $this->dbConnection->query($statement);
        return $query->fetchAll(PDO::FETCH_CLASS, Comment::class);
    }

    public function findById(int $id): ?Comment
    {
        $statement = "SELECT id, text, author, created_at AS createdAt, updated_at AS updatedAt FROM comments WHERE id=:id;";

        $preparedStatement = $this->dbConnection->prepare($statement);
        $preparedStatement->bindParam('id', $id, PDO::PARAM_INT);
        $preparedStatement->execute();
        $row = $preparedStatement->fetchObject(Comment::class);
        return !$row ? null : $row;
    }

    public function deleteById(int $id): void
    {
        $statement = "DELETE FROM comments WHERE id=:id;";

        $preparedStatement = $this->dbConnection->prepare($statement);
        $preparedStatement->bindParam('id', $id, PDO::PARAM_INT);
        $preparedStatement->execute();
    }

    public function insert(Array $input): string
    {
        $statement = "INSERT INTO comments (text, author) VALUES (:text, :author);";
        $preparedStatement = $this->dbConnection->prepare($statement);

        $preparedStatement->bindParam('text', $input['text'], PDO::PARAM_STR);
        $preparedStatement->bindParam('author', $input['author'], PDO::PARAM_STR);

        $preparedStatement->execute();

        return $this->dbConnection->lastInsertId();
    }

    public function update(int $id, Array $input, $updateDate): void
    {
        $statement = "UPDATE comments SET text=:text, author=:author, updated_at=:updatedAt WHERE id=:id;";
        $preparedStatement = $this->dbConnection->prepare($statement);

        $preparedStatement->bindParam('text', $input['text'], PDO::PARAM_STR);
        $preparedStatement->bindParam('author', $input['author'], PDO::PARAM_STR);
        $preparedStatement->bindParam('updatedAt', $updateDate, PDO::PARAM_STR);
        $preparedStatement->bindParam('id', $id, PDO::PARAM_INT);

        $preparedStatement->execute();
    }
}
