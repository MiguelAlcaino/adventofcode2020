<?php

namespace AdventOfCode\Challenges\Twelve\Model;

class Ship
{
    private int    $x;
    private int    $y;
    private int    $direction;

    public function __construct()
    {
        $this->x         = 0;
        $this->y         = 0;
        $this->direction = 0;
    }

    public function move(Movement $movement)
    {
        switch ($movement->getAction()) {
            case 'L':
                $this->direction = abs($this->direction + $movement->getValue()) % 360;
                break;
            case 'R':
                $this->direction = abs($this->direction - $movement->getValue() + 360) % 360;
                break;
            case 'F':
                switch ($this->direction) {
                    case 0:
                        $this->x = $this->x + $movement->getValue();
                        break;
                    case 90:
                        $this->y = $this->y + $movement->getValue();
                        break;
                    case 180:
                        $this->x = $this->x - $movement->getValue();
                        break;
                    case 270:
                        $this->y = $this->y - $movement->getValue();
                        break;
                }
                break;
            case 'N':
                $this->y = $this->y + $movement->getValue();
                break;
            case 'W':
                $this->x = $this->x - $movement->getValue();
                break;
            case 'S':
                $this->y = $this->y - $movement->getValue();
                break;
            case 'E':
                $this->x = $this->x + $movement->getValue();
                break;
        }
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getPosition()
    {
        return sprintf('x: %d, y: %d, direction: %d', $this->x, $this->y, $this->direction);
    }
}
