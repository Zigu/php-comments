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
        $statement = "SELECT id, text, author, created_at, updated_at FROM comments;";
        $query = $this->dbConnection->query($statement);
        return $query->fetchAll(PDO::FETCH_CLASS, Comment::class);
    }

    public function findById(int $id): Comment
    {
        $statement = "SELECT id, text, author, created_at, updated_at FROM comments WHERE id=:id;";

        $preparedStatement = $this->dbConnection->prepare($statement);
        $preparedStatement->bindParam('id', $id, PDO::PARAM_INT);
        return $preparedStatement->fetchObject(Comment::class);
    }

    public function insert(Array $input): int
    {
        $statement = "INSERT INTO comments (text, author, created_at) VALUES (:text, :author, :createdAt) OUTPUT INSERTED.id VALUES (?);";
        $preparedStatement = $this->db->prepare($statement);

        $preparedStatement->bindParam('text', $input['text'], PDO::PARAM_STR);
        $preparedStatement->bindParam('author', $input['author'], PDO::PARAM_STR);
        $preparedStatement->bindParam('createdAt', time(), PDO::PARAM_INT);

        $preparedStatement->execute();

        $createdId = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        return $createdId['id'];
    }
}
