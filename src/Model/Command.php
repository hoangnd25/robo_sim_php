<?php

namespace RobotSimulator\Model;

use RobotSimulator\Enum\CommandType;

class Command
{
    protected CommandType $type;
    protected ?Position $position;

    public function __construct(CommandType $type, ?Position $position = null)
    {
        $this->type = $type;
        $this->position = $position;
    }

    public function getType(): CommandType
    {
        return $this->type;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }
}
