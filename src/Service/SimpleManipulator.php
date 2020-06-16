<?php

namespace RobotSimulator\Service;

use RobotSimulator\Enum\Direction;
use RobotSimulator\Exception\ExecuteCommandException;
use RobotSimulator\Model\Board;
use RobotSimulator\Model\Position;
use RobotSimulator\Interfaces\Manipulator;

class SimpleManipulator implements Manipulator
{
    public const OUT_OF_BOUND_ERROR = 'Out of bound: %s';
    public const POSITION_NOT_FOUND_ERROR = 'Position not found';
    public const FALL_OFF_BOARD_ERROR = 'Cannot move, would fall off board: %s';

    /**
     * @inheritDoc
     */
    public function place(Board $board, Position $newPosition): Position
    {
        if (!$this->isWithinBoundary($board, $newPosition)) {
            throw new ExecuteCommandException(sprintf(self::OUT_OF_BOUND_ERROR, (string)$newPosition));
        }
        return $board->setRobotPosition($newPosition);
    }

    /**
     * @inheritDoc
     */
    public function move(Board $board): Position
    {
        $position = $this->getPositionOrError($board);
        $newPosition = clone $position;
        switch ($position->getDirection()) {
            case Direction::NORTH():
                $newPosition->setY($newPosition->getY() + 1);
                break;
            case Direction::EAST():
                $newPosition->setX($newPosition->getX() + 1);
                break;
            case Direction::SOUTH():
                $newPosition->setY($newPosition->getY() - 1);
                break;
            case Direction::WEST():
                $newPosition->setX($newPosition->getX() - 1);
                break;
        }

        if (!$this->isWithinBoundary($board, $newPosition)) {
            throw new ExecuteCommandException(sprintf(self::FALL_OFF_BOARD_ERROR, (string)$newPosition));
        }

        $board->setRobotPosition($newPosition);
        return $newPosition;
    }

    /**
     * @inheritDoc
     */
    public function left(Board $board): Position
    {
        $position = $this->getPositionOrError($board);
        $position->setDirection($position->getDirection()->rotate(false));
        return $position;
    }

    /**
     * @inheritDoc
     */
    public function right(Board $board): Position
    {
        $position = $this->getPositionOrError($board);
        $position->setDirection($position->getDirection()->rotate());
        return $position;
    }

    /**
     * Check whether a position is within a given board boundary
     *
     * @param Board $board
     * @param Position $position
     * @return bool
     */
    protected function isWithinBoundary(Board $board, Position $position): bool
    {
        $x = $position->getX();
        $y = $position->getY();
        $boardSize = $board->getSize();
        return $x >= 0 && $x < $boardSize && $y >= 0 && $y < $boardSize;
    }

    /**
     * Get current robot position from a given board
     * Throw exception when position not found
     *
     * @param Board $board
     * @return Position
     * @throws ExecuteCommandException
     */
    protected function getPositionOrError(Board $board): Position
    {
        $position = $board->getRobotPosition();
        if ($position === null) {
            throw new ExecuteCommandException(self::POSITION_NOT_FOUND_ERROR);
        }

        return $position;
    }
}
