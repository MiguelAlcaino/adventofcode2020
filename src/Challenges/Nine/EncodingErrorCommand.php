<?php

namespace AdventOfCode\Challenges\Nine;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncodingErrorCommand extends Command
{
    protected static       $defaultName = 'adventofcode:9';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 9 "Encoding Error"');
    }

    private function getNumbers(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
    }

    /**
     * @param int[]           $numbers
     * @param OutputInterface $output
     */
    private function part1(array $numbers, OutputInterface $output)
    {
        $length = 25;
        $offset = 0;
        for ($i = $length; $i < count($numbers); $i++) {
            if (null === $this->findToNumbersThatSum($numbers[$i], array_slice($numbers, $offset, $length))) {
                $output->writeln(sprintf('The number not found: %d', $numbers[$i]));

                return;
            }
            $offset++;
        }
    }

    private function findToNumbersThatSum(int $numberToFind, array $numbers): ?array
    {
        foreach ($numbers as $key => $number) {
            if ($number >= $numberToFind) {
                continue;
            }
            foreach ($numbers as $secondKey => $secondNumber) {
                if ($key === $secondKey) {
                    continue;
                }

                if ($number + $secondNumber === $numberToFind) {
                    return [$number, $secondNumber];
                }
            }
        }

        return null;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numbers = $this->getNumbers();
        $start   = microtime(true);
        $this->part1($numbers, $output);

        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
