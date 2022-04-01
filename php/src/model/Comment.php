<?php declare(strict_types=1);
final class Comment
{
    private $text;

    private function __construct(string $text)
    {
        $this->text = $text;
    }

    public static function fromString(string $text): self
    {
        return new self($text);
    }

    public function __toString(): string
    {
        return $this->text;
    }
}
