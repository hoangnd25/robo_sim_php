<?php

namespace RobotSimulator\Model;

class Board
{
    protected int $size;

    protected ?Position $robotPosition;

    public function __construct(int $size, ?Position $robotPosition = null)
    {
        $this->size = $size;
        $this->robotPosition = $robotPosition;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getRobotPosition(): ?Position
    {
        return $this->robotPosition;
    }

    public function setRobotPosition(Position $robotPosition): Position
    {
        $this->robotPosition = $robotPosition;
        return $this->robotPosition;
    }
}
