<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testJSONSerialization(): void
    {
        $date = date(DatabaseConnector::DATE_FORMAT);
        $comment = Comment::newInstance(1, "phpunit text", "phpunit author", $date, $date);

        $serialized = json_encode($comment);

        $this->assertJsonStringEqualsJsonString('{"id":1,"text":"phpunit text","author":"phpunit author","createdAt":"'.$date.'","updatedAt":"'.$date.'"}', $serialized);
    }
}
