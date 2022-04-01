<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    const SAMPLE_TEXT = 'test text';

    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertInstanceOf(Comment::class, Comment::fromString(self::SAMPLE_TEXT));
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(self::SAMPLE_TEXT, Comment::fromString(self::SAMPLE_TEXT));
    }
}
