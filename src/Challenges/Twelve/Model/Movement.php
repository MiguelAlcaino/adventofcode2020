<?php

namespace AdventOfCode\Challenges\Twelve\Model;

class Movement
{
    private string $action;
    private int    $value;

    public function __construct(string $action, int $value)
    {
        $this->action = $action;
        $this->value  = $value;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
