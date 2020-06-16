<?php

namespace RobotSimulator\Model;

use RobotSimulator\Enum\Direction;

class Position
{
    protected int $x;

    protected int $y;

    protected Direction $direction;

    public function __construct(int $x, int $y, Direction $direction)
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;
        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;
        return $this;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setDirection(Direction $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function __toString()
    {
        return sprintf(
            '%d,%d,%s',
            $this->getX(),
            $this->getY(),
            $this->getDirection()->getValue(),
        );
    }
}
