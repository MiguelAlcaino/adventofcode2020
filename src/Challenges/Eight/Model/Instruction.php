<?php

namespace AdventOfCode\Challenges\Eight\Model;

class Instruction
{
    private string $operation;
    private int    $value;
    private bool   $isAlreadyExecuted = false;

    public function __construct(string $operation, int $value)
    {
        $this->operation = $operation;
        $this->value     = $value;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isAlreadyExecuted(): bool
    {
        return $this->isAlreadyExecuted;
    }

    public function markAsExecuted()
    {
        $this->isAlreadyExecuted = true;
    }
}
