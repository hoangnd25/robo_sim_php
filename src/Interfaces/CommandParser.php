<?php

namespace RobotSimulator\Interfaces;

use RobotSimulator\Exception\ParseCommandException;
use RobotSimulator\Model\Command;

interface CommandParser
{
    /**
     * Convert string input into Command object
     *
     * @param string $input
     * @return Command
     * @throws ParseCommandException
     */
    public function parse(string $input): Command;
}
