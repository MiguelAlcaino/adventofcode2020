<?php

namespace AdventOfCode\Challenges\Eight\Model;

class Instruction
{
    public const OPERATION_NOP = 'nop';
    public const OPERATION_JMP = 'jmp';
    public const OPERATION_ACC = 'acc';

    private string $operation;
    private int    $value;
    private bool   $isAlreadyExecuted = false;
    private bool   $hasBeenSwapped    = false;

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

    public function markAsNotExecuted()
    {
        $this->isAlreadyExecuted = false;
    }

    public function swapJumpNop()
    {
        if ($this->operation === self::OPERATION_JMP) {
            $this->operation = self::OPERATION_NOP;
        } else {
            $this->operation = self::OPERATION_JMP;
        }

        $this->hasBeenSwapped = true;
    }

    public function hasBeenSwapped(): bool
    {
        return $this->hasBeenSwapped;
    }
}
