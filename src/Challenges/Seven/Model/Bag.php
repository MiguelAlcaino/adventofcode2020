<?php

namespace AdventOfCode\Challenges\Seven\Model;

class Bag
{
    /** @var Bag[] */
    private array  $nodes;
    private ?int   $amount;
    private string $color;

    public function __construct(string $color, int $amount = null)
    {
        $this->amount = $amount ?? null;
        $this->color  = $color;
        $this->nodes  = [];
    }

    public function addNode(Bag $node)
    {
        $this->nodes[] = $node;
    }

    public function getChildren(): array
    {
        return $this->nodes;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
