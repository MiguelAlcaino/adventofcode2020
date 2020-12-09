<?php

namespace AdventOfCode\Challenges\Eight;

use AdventOfCode\Challenges\Eight\Model\Instruction;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandheldHaltingCommand extends Command
{
    protected static       $defaultName = 'adventofcode:8';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 8 "Handheld Halting"');
    }

    private function getInstructions(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_map(
            function ($val) {
                $explodedInstruction = explode(' ', $val);

                return new Instruction($explodedInstruction[0], (int)$explodedInstruction[1]);
            },
            array_filter(
                explode("\n", $content),
                function (string $val) {
                    return !empty($val);
                }
            )
        );
    }

    /**
     * @param Instruction[]   $instructions
     * @param OutputInterface $output
     */
    private function part1(array $instructions, OutputInterface $output)
    {
        $stepper     = 0;
        $accumulator = 0;
        while (!$instructions[$stepper]->isAlreadyExecuted()) {
            $this->executeInstruction($instructions[$stepper], $accumulator, $stepper);
        }

        $output->writeln(sprintf('Part 1: Accumulator value before infinite loop: %d', $accumulator));
    }

    private function executeInstruction(Instruction $instruction, int &$accumulator, int &$stepper): void
    {
        switch ($instruction->getOperation()) {
            case 'acc':
                $accumulator = $accumulator + $instruction->getValue();
                $stepper++;
                break;
            case 'jmp':
                $stepper = $stepper + $instruction->getValue();
                break;
            case 'nop':
                $stepper++;
                break;
        }
        $instruction->markAsExecuted();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instructions = $this->getInstructions();

        $start = microtime(true);
        $this->part1($instructions, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
