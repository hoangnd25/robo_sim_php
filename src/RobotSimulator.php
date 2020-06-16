<?php

namespace RobotSimulator;

use RobotSimulator\Enum\CommandType;
use RobotSimulator\Exception\ExecuteCommandException;
use RobotSimulator\Exception\ParseCommandException;
use RobotSimulator\Model\Board;
use RobotSimulator\Interfaces\CommandParser;
use RobotSimulator\Interfaces\Manipulator;
use RobotSimulator\Model\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RobotSimulator extends SymfonyCommand
{
    protected Manipulator $manipulator;

    protected CommandParser $commandParser;

    /**
     * RobotCommand constructor.
     *
     * @param Manipulator $manipulator
     * @param CommandParser    $commandParser
     */
    public function __construct(Manipulator $manipulator, CommandParser $commandParser)
    {
        parent::__construct('robot');
        $this
            ->setDescription('A simple robot simulator')
            ->addArgument(
                'commands',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                sprintf('List of commands to be executed')
            )
            ->addOption(
                'size',
                's',
                InputOption::VALUE_OPTIONAL,
                'Set board size',
                '5'
            );
        $this->commandParser = $commandParser;
        $this->manipulator = $manipulator;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $sizeOption */
        $sizeOption = $input->getOption('size');
        $size = (int)$sizeOption;
        if ($size <= 1) {
            return $this->exitWithError($output, 'Board size must be bigger than 1');
        }

        // initialize board, board size is configurable via -s|--size flag
        $board = new Board($size);

        // only show this message in verbose mode
        $this->writeMessage($output, sprintf('Board created with %d units', $board->getSize()));

        /** @var string[]|string $commandInputs */
        $commandInputs = $input->getArgument('commands');
        // input can be either a string or array of strings
        if (!is_array($commandInputs)) {
            $commandInputs = [$commandInputs];
        }

        foreach ($commandInputs as $commandInput) {
            try {
                $command = $this->commandParser->parse($commandInput);
            } catch (ParseCommandException $ex) {
                $this->writeMessage($output, $ex->getMessage(), 'error');
                continue;
            }

            // if robot is not placed then exit early & show error message
            if ($board->getRobotPosition() === null && !$command->getType()->equals(CommandType::PLACE())) {
                return $this->exitWithError($output, 'Robot must be placed before executing other commands');
            }

            // execute all commands, ignore invalid ones & display errors
            try {
                $this->executeManipulatorCommand($board, $command, $output);
            } catch (ExecuteCommandException $ex) {
                // Only show errors in verbose mode
                $this->writeMessage($output, $ex->getMessage(), 'error');
            }
        }

        return SymfonyCommand::SUCCESS;
    }

    protected function writeMessage(
        OutputInterface $output,
        string $message,
        string $type = 'info',
        bool $verboseOnly = true
    ): void {
        $output->writeln(
            sprintf('<%s>%s</%s>', $type, $message, $type),
            $verboseOnly ? OutputInterface::VERBOSITY_VERBOSE : 0
        );
    }

    protected function exitWithError(OutputInterface $output, string $errorMessage): int
    {
        $this->writeMessage($output, $errorMessage, 'error', false);
        return SymfonyCommand::FAILURE;
    }

    /**
     * Execute a given command & print log messages
     *
     * @param Board $board
     * @param Command $command
     * @param OutputInterface $output
     * @throws ExecuteCommandException
     */
    protected function executeManipulatorCommand(Board $board, Command $command, OutputInterface $output): void
    {
        $getCurrentPosition = static function () use ($board): string {
            return (string)$board->getRobotPosition();
        };

        // map command to handler & print logs
        switch ($command->getType()) {
            case CommandType::PLACE():
                $this->manipulator->place($board, $command->getPosition()); /** @phpstan-ignore-line */
                $this->writeMessage(
                    $output,
                    sprintf('Placed: %s', $getCurrentPosition())
                );
                break;
            case CommandType::MOVE():
                $this->manipulator->move($board);
                $this->writeMessage(
                    $output,
                    sprintf('Moved: %s', $getCurrentPosition())
                );
                break;
            case CommandType::LEFT():
                $this->manipulator->left($board);
                $this->writeMessage(
                    $output,
                    sprintf('Rotated left: %s', $getCurrentPosition())
                );
                break;
            case CommandType::RIGHT():
                $this->manipulator->right($board);
                $this->writeMessage(
                    $output,
                    sprintf('Rotated right: %s', $getCurrentPosition())
                );
                break;
            case CommandType::REPORT():
            default:
                $output->writeln($getCurrentPosition());
        }
    }
}
