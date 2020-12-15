<?php

namespace AdventOfCode\Challenges\Twelve\Model;

class Ship
{
    private int      $x;
    private int      $y;
    private int      $direction;
    private Waypoint $waypoint;

    public function __construct()
    {
        $this->x         = 0;
        $this->y         = 0;
        $this->direction = 0;
    }

    public function setWaypoint(Waypoint $waypoint)
    {
        $this->waypoint = $waypoint;
    }

    public function move2(Movement $movement)
    {
        switch ($movement->getAction()) {
            case 'L':
            case 'R':
                if ($movement->getValue() === 180) {
                    $this->waypoint
                        ->setx(-1 * $this->waypoint->getX())
                        ->setY(-1 * $this->waypoint->getY());
                    break;
                }

                $direction = $movement->getValue();
                if ($movement->getAction() === 'R') {
                    $direction = ($movement->getValue() * -1) + 360;
                }

                switch ($direction) {
                    case 90:
                        $y = $this->waypoint->getY() * -1;
                        $this->waypoint->setY($this->waypoint->getX());
                        $this->waypoint->setX($y);
                        break;
                    case 270:
                        $x = $this->waypoint->getX() * -1;
                        $this->waypoint->setX($this->waypoint->getY());
                        $this->waypoint->setY($x);
                        break;
                }
                break;
            case 'F':
                $this->x = ($this->waypoint->getX() * $movement->getValue()) + $this->x;
                $this->y = ($this->waypoint->getY() * $movement->getValue()) + $this->y;
                break;
            case 'N':
                $this->waypoint->setY($this->waypoint->getY() + $movement->getValue());
                break;
            case 'W':
                $this->waypoint->setX($this->waypoint->getX() - $movement->getValue());
                break;
            case 'S':
                $this->waypoint->setY($this->waypoint->getY() - $movement->getValue());
                break;
            case 'E':
                $this->waypoint->setX($this->waypoint->getX() + $movement->getValue());
                break;
        }
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

    public function getWaypoint(): Waypoint
    {
        return $this->waypoint;
    }

    public function getPosition(): string
    {
        return sprintf('x: %d, y: %d, direction: %d', $this->x, $this->y, $this->direction);
    }
}
