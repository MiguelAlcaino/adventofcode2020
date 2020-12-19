<?php

namespace AdventOfCode\Challenges\Fourteen\Model;

class MemAddress
{
    private int $address;
    private int $originalValue;
    private int $newValue;

    public function __construct(int $address, int $originalValue)
    {
        $this->address       = $address;
        $this->originalValue = $originalValue;
    }

    public function getAddress(): int
    {
        return $this->address;
    }

    public function getOriginalValue(): int
    {
        return $this->originalValue;
    }

    public function getNewValue(): int
    {
        return $this->newValue;
    }

    public function setNewValue(int $newValue): void
    {
        $this->newValue = $newValue;
    }
}
