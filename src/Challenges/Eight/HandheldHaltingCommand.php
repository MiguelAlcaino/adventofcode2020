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

    /**
     * @param Instruction[]   $instructions
     * @param OutputInterface $output
     */
    private function part2(array $instructions, OutputInterface $output)
    {
        $isLastLine = false;
        foreach ($instructions as $key => $instruction) {
            if (
                $instruction->hasBeenSwapped()
                || ($instruction->getOperation() === Instruction::OPERATION_NOP && $instruction->getValue() === 0)
            ) {
                continue;
            }

            if ($instruction->getOperation() === Instruction::OPERATION_NOP || $instruction->getOperation() === Instruction::OPERATION_JMP) {
                //$output->writeln(sprintf('Line: %d. Instruction: %s', $key, $instruction->getOperation()));

                $instructionSwapped = $instruction;
                $instruction->swapJumpNop();
                $stepper     = 0;
                $accumulator = 0;

                while (true) {
                    if ($instructions[$stepper]->isAlreadyExecuted()) {
                        // Reverting to its original value
                        $instructionSwapped->swapJumpNop();
                        $this->resetAlreadyExecuted($instructions);
                        break;
                    }

                    if (count($instructions) === $stepper + 1) {
                        $isLastLine = true;
                    }
                    $this->executeInstruction($instructions[$stepper], $accumulator, $stepper);
                    if ($isLastLine) {
                        $output->writeln(sprintf('Part 2. Accumulator value: %d', $accumulator));

                        return;
                    }
                }
            }
        }
    }

    /**
     * @param Instruction[] $instructions
     */
    private function resetAlreadyExecuted(array $instructions)
    {
        foreach ($instructions as $instruction) {
            $instruction->markAsNotExecuted();
        }
    }

    private function executeInstruction(Instruction $instruction, int &$accumulator, int &$stepper): void
    {
        switch ($instruction->getOperation()) {
            case Instruction::OPERATION_ACC:
                $accumulator = $accumulator + $instruction->getValue();
                $stepper++;
                break;
            case Instruction::OPERATION_JMP:
                $stepper = $stepper + $instruction->getValue();
                break;
            case Instruction::OPERATION_NOP:
                $stepper++;
                break;
        }
        $instruction->markAsExecuted();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instructions = $this->getInstructions();
        $start        = microtime(true);
        $this->part1($instructions, $output);
        $instructions = $this->getInstructions();
        $this->part2($instructions, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
