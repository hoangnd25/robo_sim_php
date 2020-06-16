<?php

namespace RobotSimulator\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class ManipulatorCommandType
 *
 * @package RobotSimulator\Enum
 *
 * @extends Enum<string>
 *
 * @method static CommandType PLACE()
 * @method static CommandType MOVE()
 * @method static CommandType LEFT()
 * @method static CommandType RIGHT()
 * @method static CommandType REPORT()
 */
class CommandType extends Enum
{
    private const PLACE = 'PLACE';

    private const MOVE = 'MOVE';

    private const LEFT = 'LEFT';

    private const RIGHT = 'RIGHT';

    private const REPORT = 'REPORT';
}
