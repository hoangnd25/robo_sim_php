<?php

namespace RobotSimulator\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class FacingDirection
 *
 * @package RobotSimulator\Enum
 *
 * @extends Enum<string>
 *
 * @method static Direction NORTH()
 * @method static Direction WEST()
 * @method static Direction SOUTH()
 * @method static Direction EAST()
 */
class Direction extends Enum
{
    private const NORTH = 'NORTH';

    private const EAST = 'EAST';

    private const SOUTH = 'SOUTH';

    private const WEST = 'WEST';

    private const ORDERED_LIST = [self::NORTH, self::EAST, self::SOUTH, self::WEST];

    /**
     * Return rotated Object
     *
     * @param bool $clockwise
     * @return Direction
     */
    public function rotate(bool $clockwise = true): self
    {
        /** @var int $index */
        $index = array_search($this->getValue(), self::ORDERED_LIST, true);

        // move to next or previous item of the array depending on rotating direction
        if ($clockwise) {
            ++$index;
        } else {
            --$index;
        }

        // jump to start or end of the array depending on rotating direction
        $count = count(self::ORDERED_LIST);
        if ($index < 0) {
            $index += $count;
        } elseif ($index >= $count) {
            $index -= $count;
        }

        return new self(self::ORDERED_LIST[$index]);
    }
}
