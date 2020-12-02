<?php

namespace AdventOfCode\Challenges\One;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ReportRepairCommand extends Command
{
    protected static $defaultName = 'adventofcode:1';

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 1 "Report Repair"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lines = $this->getNumbers();

        $start = microtime(true);
        $this->part1($lines, $output);
        $this->part2($lines, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }

    /**
     * @param int[] $lines
     */
    private function part1(array $lines, OutputInterface $output): void
    {
        foreach ($lines as $key => $number) {
            if ($number >= 2020) {
                continue;
            }
            foreach ($lines as $secondKey => $secondNumber) {
                if ($key === $secondKey) {
                    continue;
                }

                if ($number + $secondNumber === 2020) {
                    $output->writeln(sprintf('The two numbers that sum 2020 are %d and %d. And they multiply %d. Merry Christmas!', $number, $secondNumber, $number * $secondNumber));

                    return;
                }
            }
        }
    }

    /**
     * @param int[] $lines
     */
    private function part2(array $lines, OutputInterface $output): void
    {
        foreach ($lines as $key => $number) {
            foreach ($lines as $secondKey => $secondNumber) {
                if ($key == $secondKey || ($sumFirstAndSecond = $number + $secondNumber) > 2020) {
                    continue;
                }
                foreach ($lines as $thirdKey => $thirdNumber) {
                    if ($key === $secondKey && $secondKey === $thirdKey) {
                        continue;
                    }

                    $totalSum = $sumFirstAndSecond + $thirdNumber;
                    if ($totalSum === 2020) {
                        $output->writeln(sprintf('The three numbers that sum 2020 are %d, %d and %d. And they multiply %d. Merry Christmas!', $number, $secondNumber, $thirdNumber, $number * $secondNumber * $thirdNumber));

                        return;
                    }
                }
            }
        }
    }

    private function getNumbers(): array
    {
        $finder = new Finder();
        // find all files in the current directory
        $files = $finder->files()->in(__DIR__ . '/Resources/');
        $files->name('input.txt');
        foreach ($files->getIterator() as $fileInfo) {
            $content = $fileInfo->getContents();
        }

        $filtered = array_filter(
            explode("\n", $content),
            function ($val) {
                return is_numeric($val);
            }
        );

        return array_map(
            function (string $value) {
                return (int)$value;
            },
            $filtered
        );
    }
}
