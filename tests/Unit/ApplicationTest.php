<?php

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use RobotSimulator\Service\SimpleCommandParser;
use RobotSimulator\Service\SimpleManipulator;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use RobotSimulator\RobotSimulator;

class ApplicationTest extends TestCase
{
    /**
     * @dataProvider executeData
     * @param $commands
     * @param $expectedOutput
     * @param $verbose
     */
    public function testExecute($commands, $expectedOutput, $verbose = false): void
    {
        $tester = $this->createTester();
        $tester->execute(['commands' => $commands], [
            'verbosity' => $verbose ? ConsoleOutput::VERBOSITY_VERBOSE : ConsoleOutput::VERBOSITY_NORMAL,
        ]);

        $output = $tester->getDisplay();
        $this->assertEquals($expectedOutput, $output);
    }

    public function executeData(): array
    {
        return [
            [
                [
                    'PLACE 0,0,NORTH',
                    'MOVE',
                    'REPORT'
                ],
                "0,1,NORTH\n"
            ],
            [
                [
                    'PLACE 0,0,NORTH',
                    'LEFT',
                    'REPORT'
                ],
                "0,0,WEST\n"
            ],
            [
                [
                    'PLACE 1,2,EAST',
                    'MOVE',
                    'MOVE',
                    'LEFT',
                    'MOVE',
                    'REPORT'
                ],
                "3,3,NORTH\n"
            ],
            [
                [
                    'PLACE 1,1,SOUTH',
                    'RIGHT',
                    'MOVE',
                    'MOVE',
                    'LEFT',
                    'REPORT'
                ],
                "0,1,SOUTH\n"
            ],
            [
                'PLACE 1,1,SOUTH',
                "Board created with 5 units\nPlaced: 1,1,SOUTH\n",
                true
            ],
            [
                [
                    'PLACE 2,3,WEST',
                    'LEFT',
                    'MOVE',
                    'RIGHT',
                    'MOVE',
                    'REPORT',
                ],
                "Board created with 5 units\n" .
                "Placed: 2,3,WEST\n" .
                "Rotated left: 2,3,SOUTH\n" .
                "Moved: 2,2,SOUTH\n" .
                "Rotated right: 2,2,WEST\n" .
                "Moved: 1,2,WEST\n" .
                "1,2,WEST\n",
                true
            ],
            [
                [
                    'PLACE 4,4,NORTH',
                    'MOVE', // this command will be ignored
                    'RIGHT',
                    'MOVE', // this command will be ignored
                    'RIGHT',
                    'MOVE',
                    'REPORT'
                ],
                "4,3,SOUTH\n",
            ],
            [
                [
                    'PLACE 4,4,NORTH',
                    'MOVE', // this command will be ignored
                    'RIGHT',
                    'MOVE', // this command will be ignored
                    'RIGHT',
                    'MOVE',
                    'REPORT'
                ],
                "Board created with 5 units\n" .
                "Placed: 4,4,NORTH\n" .
                "Cannot move, would fall off board: 4,5,NORTH\n" .
                "Rotated right: 4,4,EAST\n" .
                "Cannot move, would fall off board: 5,4,EAST\n" .
                "Rotated right: 4,4,SOUTH\n" .
                "Moved: 4,3,SOUTH\n" .
                "4,3,SOUTH\n"
                ,
                true
            ],
            [
                [
                    'PLACE 4,4,NORTH',
                    'JUMP', // this command will be ignored
                    'LEFT'
                ],
                "Board created with 5 units\n" .
                "Placed: 4,4,NORTH\n" .
                "Not supported command: JUMP\n" .
                "Rotated left: 4,4,WEST\n"
                ,
                true
            ]
        ];
    }

    public function testInvalidBoardSize(): void
    {
        $tester = $this->createTester();
        $tester->execute([
            'commands' => [],
            '--size' => 1,
        ]);

        $output = $tester->getDisplay();
        $this->assertEquals("Board size must be bigger than 1\n", $output);
    }

    public function testMoveBeforePlace(): void
    {
        $tester = $this->createTester();
        $tester->execute([
            'commands' => [
                'MOVE'
            ]
        ]);

        $output = $tester->getDisplay();
        $this->assertEquals("Robot must be placed before executing other commands\n", $output);
    }

    protected function createTester(): CommandTester
    {
        return new CommandTester(new RobotSimulator(
            new SimpleManipulator(),
            new SimpleCommandParser()
        ));
    }
}
