<?php declare(strict_types=1);

class ResponseRenderer
{
    public function renderHeader($value): void
    {
        header($value);
    }

    public function renderBody($value): void
    {
        echo $value;
    }
}
