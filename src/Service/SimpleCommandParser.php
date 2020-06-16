<?php

namespace RobotSimulator\Service;

use RobotSimulator\Enum\Direction;
use RobotSimulator\Enum\CommandType;
use RobotSimulator\Exception\ParseCommandException;
use RobotSimulator\Model\Command;
use RobotSimulator\Interfaces\CommandParser;
use RobotSimulator\Model\Position;
use UnexpectedValueException;

class SimpleCommandParser implements CommandParser
{
    public const UNSUPPORTED_COMMAND_ERROR = 'Not supported command: %s';
    public const MISSING_POSITION_ERROR = 'Missing position for command: %s';
    public const INVALID_DIRECTION_ERROR = 'Invalid direction for command: %s';
    public const INVALID_COORDINATE_ERROR = 'Invalid coordinate for command: %s';

    /**
     * @inheritDoc
     */
    public function parse(string $input): Command
    {
        $parts = explode(' ', $input, 2);
        $commandTypeInput = trim($parts[0] ?? '');
        try {
            $commandType = new CommandType($commandTypeInput);
        } catch (UnexpectedValueException $ex) {
            throw new ParseCommandException(sprintf(self::UNSUPPORTED_COMMAND_ERROR, $input));
        }

        if (!$commandType->equals(CommandType::PLACE())) {
            return new Command($commandType);
        }

        $positionInput = trim($parts[1] ?? '');
        if ($positionInput === '') {
            throw new ParseCommandException(sprintf(self::MISSING_POSITION_ERROR, $input));
        }

        $positionParts = explode(',', str_replace(' ', '', $positionInput));
        $facingDirectionInput = $positionParts[2] ?? '';
        try {
            $facingDirection = new Direction($facingDirectionInput);
        } catch (UnexpectedValueException $ex) {
            throw new ParseCommandException(sprintf(self::INVALID_DIRECTION_ERROR, $input));
        }

        $xInput = $positionParts[0] ?? '';
        $yInput = $positionParts[1] ?? '';

        if (!is_numeric($xInput) || !is_numeric($yInput)) {
            throw new ParseCommandException(sprintf(self::INVALID_COORDINATE_ERROR, $input));
        }

        return new Command($commandType, new Position((int)$xInput, (int)$yInput, $facingDirection));
    }
}
