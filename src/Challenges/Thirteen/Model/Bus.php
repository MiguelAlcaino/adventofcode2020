<?php

declare(strict_types=1);

namespace AdventOfCode\Challenges\Thirteen\Model;

class Bus
{
    private int $id;
    private int $offset;

    public function __construct(int $id, int $offset)
    {
        $this->id     = $id;
        $this->offset = $offset;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
