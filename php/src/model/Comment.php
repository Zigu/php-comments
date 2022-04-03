<?php declare(strict_types=1);

final class Comment implements JsonSerializable
{
    private $id;
    private $text;
    private $author;
    private $createdAt;
    private $updatedAt;

    public static function newInstance(int $id, string $text, string $author, $createdAt, $updatedAt): Comment
    {
        $comment = new Comment();
        $comment->setId($id);
        $comment->setText($text);
        $comment->setAuthor($author);
        $comment->setCreatedAt($createdAt);
        $comment->setUpdatedAt($updatedAt);
        return $comment;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
