<?php

namespace AdventOfCode\Challenges\Fourteen\Model;

class Group
{
    private string $mask;
    /**
     * @var MemAddress[]
     */
    private array  $memAddresses;

    public function __construct(string $mask)
    {
        $this->mask         = $mask;
        $this->memAddresses = [];
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function getMemAddresses(): array
    {
        return $this->memAddresses;
    }

    public function addMemAddress(MemAddress $memAddress): void
    {
        $this->memAddresses[] = $memAddress;
    }
}
