<?php

namespace AdventOfCode\Challenges\Five;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BinaryBoardingCommand extends Command
{
    protected static       $defaultName = 'adventofcode:5';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    private function getSeats(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
    }

    private function part1(array $seats, OutputInterface $output): void
    {
        $ids = [];
        foreach ($seats as $seat) {
            $row    = $this->getSeatRow(str_split(substr($seat, 0, 7)), 0, 128);
            $column = $this->getSeatRow(str_split(substr($seat, 7, 3)), 0, 8);
            $ids[]  = $row * 8 + $column;
        }

        $output->writeln(sprintf('The max id is %d', max($ids)));
    }

    private function getSeatRow(array $mysteryPositions, int $index = 0, int $length = 128)
    {
        if (count($mysteryPositions) === 0) {
            return $index;
        }
        $frontOrBack = array_shift($mysteryPositions);

        $halfOfRows = ($length + $index) / 2;
        if ($frontOrBack === 'F' || $frontOrBack === 'L') {
            return $this->getSeatRow($mysteryPositions, $index, $halfOfRows);
        } elseif ($frontOrBack === 'B' || $frontOrBack === 'R') {
            return $this->getSeatRow($mysteryPositions, $halfOfRows, $length);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $seats = $this->getSeats();

        $start = microtime(true);
        $this->part1($seats, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
