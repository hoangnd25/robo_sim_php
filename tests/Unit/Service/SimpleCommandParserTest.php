<?php

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use RobotSimulator\Enum\CommandType;
use RobotSimulator\Enum\Direction;
use RobotSimulator\Exception\ParseCommandException;
use RobotSimulator\Service\SimpleCommandParser;

class SimpleCommandParserTest extends TestCase
{
    /**
     * @dataProvider parseData
     *
     * @param string $input
     * @param CommandType $type
     * @param int $x
     * @param int $y
     * @param Direction $direction
     * @throws ParseCommandException
     */
    public function testParse(
        string $input,
        CommandType $type,
        ?int $x = null,
        ?int $y = null,
        ?Direction $direction = null
    ): void {
        $parser = new SimpleCommandParser();
        $actual = $parser->parse($input);
        $this->assertTrue($type->equals($actual->getType()));

        if ($type->equals(CommandType::PLACE())) {
            $this->assertEquals($x, $actual->getPosition()->getX());
            $this->assertEquals($y, $actual->getPosition()->getY());
            $this->assertTrue($direction->equals($actual->getPosition()->getDirection()));
        }
    }

    public function parseData(): array
    {
        return [
            ['PLACE 1,2,NORTH', CommandType::PLACE(), 1, 2, Direction::NORTH()],
            ['PLACE 2, 3, SOUTH', CommandType::PLACE(), 2, 3, Direction::SOUTH()],
            ['PLACE  9 , 10 , WEST', CommandType::PLACE(), 9, 10, Direction::WEST()],
            ['MOVE', CommandType::MOVE()],
            ['LEFT', CommandType::LEFT()],
            ['RIGHT', CommandType::RIGHT()],
            ['REPORT', CommandType::REPORT()],
        ];
    }

    /**
     * @dataProvider exceptionData
     *
     * @param string $input
     * @param string $errorMessage
     * @throws ParseCommandException
     */
    public function testException(string $input, string $errorMessage): void
    {
        $this->expectExceptionObject(
            new ParseCommandException(sprintf($errorMessage, $input))
        );

        $parser = new SimpleCommandParser();
        $parser->parse($input);
    }

    public function exceptionData(): array
    {
        return [
            ['TEST', SimpleCommandParser::UNSUPPORTED_COMMAND_ERROR],
            ['UNSUPPORTED', SimpleCommandParser::UNSUPPORTED_COMMAND_ERROR],
            ['PLACE', SimpleCommandParser::MISSING_POSITION_ERROR],
            ['PLACE 1,2', SimpleCommandParser::INVALID_DIRECTION_ERROR],
            ['PLACE 1,2,RIGHT', SimpleCommandParser::INVALID_DIRECTION_ERROR],
            ['PLACE a,2,NORTH', SimpleCommandParser::INVALID_COORDINATE_ERROR],
        ];
    }
}
