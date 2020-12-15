<?php

namespace AdventOfCode\Challenges\Twelve\Model;

class Waypoint
{

    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setX($x): self
    {
        $this->x = $x;

        return $this;
    }

    public function setY($y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getPosition(): string
    {
        return sprintf('Waypoint: x: %d, y: %d.', $this->x, $this->y);
    }
}
