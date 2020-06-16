<?php

namespace RobotSimulator\Interfaces;

use RobotSimulator\Exception\ExecuteCommandException;
use RobotSimulator\Model\Board;
use RobotSimulator\Model\Position;

interface Manipulator
{
    /**
     * Set robot position for a given board
     *
     * @param Board $board
     * @param Position $position
     * @return Position
     * @throws ExecuteCommandException
     */
    public function place(Board $board, Position $position): Position;

    /**
     * Move robot forward
     *
     * @param Board $board
     * @return Position
     * @throws ExecuteCommandException
     */
    public function move(Board $board): Position;

    /**
     * Rotate left
     *
     * @param Board $board
     * @return Position
     * @throws ExecuteCommandException
     */
    public function left(Board $board): Position;

    /**
     * Rotate right
     *
     * @param Board $board
     * @return Position
     * @throws ExecuteCommandException
     */
    public function right(Board $board): Position;
}
