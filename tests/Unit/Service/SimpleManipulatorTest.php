<?php

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use RobotSimulator\Enum\Direction;
use RobotSimulator\Exception\ExecuteCommandException;
use RobotSimulator\Model\Board;
use RobotSimulator\Model\Position;
use RobotSimulator\Service\SimpleManipulator;

class SimpleManipulatorTest extends TestCase
{

    public function testPlace(): void
    {
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, new Position(1, 2, Direction::SOUTH()));

        $this->assertEquals(1, $board->getRobotPosition()->getX());
        $this->assertEquals(2, $board->getRobotPosition()->getY());
        $this->assertTrue(Direction::SOUTH()->equals($board->getRobotPosition()->getDirection()));
    }

    public function testPlaceException(): void
    {
        $newPosition = new Position(6, 6, Direction::SOUTH());
        $this->expectExceptionObject(new ExecuteCommandException(
            sprintf(SimpleManipulator::OUT_OF_BOUND_ERROR, (string)$newPosition)
        ));
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, $newPosition);
    }

    /**
     * @dataProvider moveData
     *
     * @param Position $initialPosition
     * @param Position $expectedPosition
     * @throws ExecuteCommandException
     */
    public function testMove(Position $initialPosition, Position $expectedPosition): void
    {
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, $initialPosition);
        $manipulator->move($board);

        $this->assertEquals($expectedPosition->getX(), $board->getRobotPosition()->getX());
        $this->assertEquals($expectedPosition->getY(), $board->getRobotPosition()->getY());
        $this->assertTrue($expectedPosition->getDirection()->equals($board->getRobotPosition()->getDirection()));
    }

    public function moveData(): array
    {
        return [
            [new Position(1, 2, Direction::NORTH()), new Position(1, 3, Direction::NORTH())],
            [new Position(1, 2, Direction::EAST()), new Position(2, 2, Direction::EAST())],
            [new Position(1, 2, Direction::SOUTH()), new Position(1, 1, Direction::SOUTH())],
            [new Position(1, 2, Direction::WEST()), new Position(0, 2, Direction::WEST())],
        ];
    }

    public function testMoveWithoutPlacing(): void
    {
        $this->expectExceptionObject(new ExecuteCommandException(SimpleManipulator::POSITION_NOT_FOUND_ERROR));
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->move($board);
    }

    public function testMoveFallOffBoard(): void
    {
        $initialPosition = new Position(1, 4, Direction::NORTH());
        $this->expectExceptionObject(
            new ExecuteCommandException(
                sprintf(SimpleManipulator::FALL_OFF_BOARD_ERROR, '1,5,NORTH')
            )
        );
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, $initialPosition);
        $manipulator->move($board);
    }

    /**
     * @dataProvider leftData
     *
     * @param Position $initialPosition
     * @param Direction $expectedDirection
     * @throws ExecuteCommandException
     */
    public function testLeft(Position $initialPosition, Direction $expectedDirection): void
    {
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, $initialPosition);
        $manipulator->left($board);

        $this->assertEquals($initialPosition->getX(), $board->getRobotPosition()->getX());
        $this->assertEquals($initialPosition->getY(), $board->getRobotPosition()->getY());
        $this->assertTrue($expectedDirection->equals($board->getRobotPosition()->getDirection()));
    }

    public function leftData(): array
    {
        return [
            [new Position(1, 2, Direction::NORTH()), Direction::WEST()],
            [new Position(1, 2, Direction::WEST()), Direction::SOUTH()],
            [new Position(1, 2, Direction::SOUTH()), Direction::EAST()],
            [new Position(1, 2, Direction::EAST()),  Direction::NORTH()],
        ];
    }

    /**
     * @dataProvider rightData
     *
     * @param Position $initialPosition
     * @param Direction $expectedDirection
     * @throws ExecuteCommandException
     */
    public function testRight(Position $initialPosition, Direction $expectedDirection): void
    {
        $board = new Board(5);
        $manipulator = new SimpleManipulator();
        $manipulator->place($board, $initialPosition);
        $manipulator->right($board);

        $this->assertEquals($initialPosition->getX(), $board->getRobotPosition()->getX());
        $this->assertEquals($initialPosition->getY(), $board->getRobotPosition()->getY());
        $this->assertTrue($expectedDirection->equals($board->getRobotPosition()->getDirection()));
    }

    public function rightData(): array
    {
        return [
            [new Position(1, 2, Direction::NORTH()), Direction::EAST()],
            [new Position(1, 2, Direction::EAST()), Direction::SOUTH()],
            [new Position(1, 2, Direction::SOUTH()), Direction::WEST()],
            [new Position(1, 2, Direction::WEST()),  Direction::NORTH()],
        ];
    }
}
