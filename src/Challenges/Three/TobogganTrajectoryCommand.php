<?php

namespace AdventOfCode\Challenges\Three;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TobogganTrajectoryCommand extends Command
{
    protected static $defaultName = 'adventofcode:3';

    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 3 "Toboggan Trajectory"');
    }

    private function getMap(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_map(
            function (string $line) {
                return str_split($line);
            },
            array_filter(
                explode("\n", $content),
                function (string $line) {
                    return !empty($line);
                }
            )
        );
    }

    private function part1(array $map, OutputInterface $output)
    {
        $numberOfTreesFound = $this->getNumberOfTreesFound($map, 3, 1);
        $output->writeln(sprintf('%d trees were found while going down', $numberOfTreesFound));
    }

    private function part2(array $map, OutputInterface $output)
    {
        $result =
            $this->getNumberOfTreesFound($map, 1, 1)
            * $this->getNumberOfTreesFound($map, 3, 1)
            * $this->getNumberOfTreesFound($map, 5, 1)
            * $this->getNumberOfTreesFound($map, 7, 1)
            * $this->getNumberOfTreesFound($map, 1, 2);

        $output->writeln(sprintf('This is the product of the trees found: %d', $result));
    }

    private function getNumberOfTreesFound(
        array $map,
        int $goRight = 3,
        int $goDown = 1,
        int $column = 0,
        int $row = 0,
        OutputInterface $output = null
    ): int {
        $mapCopy = $map;
        // Exit condition. To check if we are at the bottom of the map
        if (!isset($map[$row + $goDown])) {
            return 0;
        } // If there's no space to the right then attach another map to the right
        elseif (!isset($map[$row][$column + $goRight])) {
            $numberOfColumnsOfThisRow = count($map[$row]) - $column;
            $column                   = $goRight - $numberOfColumnsOfThisRow;
        } else {
            $column = $column + $goRight;
        }
        $row  = $row + $goDown;
        $cell = $map[$row][$column];

        if ($cell === '#') {
            if (null !== $output) {
                $mapCopy[$row][$column] = 'X';
                $output->writeln(implode('', $mapCopy[$row]));
            }

            return $this->getNumberOfTreesFound($map, $goRight, $goDown, $column, $row, $output) + 1;
        } else {
            if (null !== $output) {
                $mapCopy[$row][$column] = '0';
                $output->writeln(implode('', $mapCopy[$row]));
            }

            return $this->getNumberOfTreesFound($map, $goRight, $goDown, $column, $row, $output) + 0;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $map = $this->getMap();

        $start = microtime(true);
        $this->part1($map, $output);
        $this->part2($map, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
