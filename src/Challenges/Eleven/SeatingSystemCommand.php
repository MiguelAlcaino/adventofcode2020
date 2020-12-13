<?php

namespace AdventOfCode\Challenges\Eleven;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeatingSystemCommand extends Command
{
    protected static        $defaultName = 'adventofcode:11';
    private InputExtractor  $inputExtractor;
    private OutputInterface $output;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 11 "Seating System"');
    }

    private function getLayout(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        $lines = array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );

        $seatLayout = [];
        foreach ($lines as $key => $line) {
            $seatLayout[] = str_split($line);
        }

        return $seatLayout;
    }

    private function part1(array $seatLayout)
    {
        $this->printLayout($seatLayout);
        $arePreviousAndNewLayoutEquals = false;
        $loopCounter                   = 0;
        while (!$arePreviousAndNewLayoutEquals) {
            $newLayout = $this->processSeatingOrLeaving($seatLayout);

            if ($newLayout == $seatLayout) {
                $arePreviousAndNewLayoutEquals = true;
            } else {
                $seatLayout = $newLayout;
            }
            $loopCounter++;
            $this->output->writeln($loopCounter);
        }

        $this->output->writeln('---------------------');
        $this->printLayout($seatLayout);
        $this->output->writeln(sprintf('Number of occupied seats is %d', $this->countOccupiedSeats($seatLayout)));
    }

    private function countOccupiedSeats(array $layout)
    {
        $counter = 0;
        for ($i = 0; $i < count($layout); $i++) {
            for ($j = 0; $j < count($layout[$i]); $j++) {
                if ($layout[$i][$j] === '#') {
                    $counter++;
                }
            }
        }

        return $counter;
    }

    private function processSeatingOrLeaving(array $currentStateOfSeatingLayout)
    {
        $newLayout = $currentStateOfSeatingLayout;
        for ($i = 0; $i < count($currentStateOfSeatingLayout); $i++) {
            for ($j = 0; $j < count($currentStateOfSeatingLayout[$i]); $j++) {
                $isSeatFree = $this->isSeatFree($i, $j, $currentStateOfSeatingLayout);
                if ($isSeatFree === true) {
                    $newLayout[$i][$j] = $this->occupySeatIfPossible($i, $j, $currentStateOfSeatingLayout);
                } elseif ($isSeatFree === false) {
                    $newLayout[$i][$j] = $this->freeSeatIfPossible($i, $j, $currentStateOfSeatingLayout);
                }
            }
        }

        return $newLayout;
    }

    private function freeSeatIfPossible(int $i, int $j, array $layout)
    {
        $occupiedSeatCounter = 0;
        for ($y = $i - 1; $y < $i + 2; $y++) {
            for ($z = $j - 1; $z < $j + 2; $z++) {
                // Same position
                if ($y === $i && $z === $j) {
                    continue;
                }
                // If positions don't exist, do nothing
                if (!isset($layout[$y][$z])) {
                    continue;
                }
                if ($layout[$y][$z] === '#') {
                    $occupiedSeatCounter++;
                }
                if ($occupiedSeatCounter >= 4) {
                    return 'L';
                }
            }
        }

        return $layout[$i][$j];
    }

    /**
     * @return bool true for free, false for occupied, null for floor
     */
    private function isSeatFree(
        int $i,
        int $j,
        array $layout
    ): ?bool {
        switch ($layout[$i][$j]) {
            case '#':
                return false;
            case 'L':
                return true;
            default:
                return null;
        }
    }

    private function occupySeatIfPossible(
        int $i,
        int $j,
        array $layout
    ) {
        $freeSeatsCounter = 0;
        for ($y = $i - 1; $y < $i + 2; $y++) {
            for ($z = $j - 1; $z < $j + 2; $z++) {
                // Same position
                if ($y === $i && $z === $j) {
                    continue;
                }
                // If positions don't exist, then free seat
                if (!isset($layout[$y][$z])) {
                    $freeSeatsCounter++;
                    continue;
                }
                if ($layout[$y][$z] === 'L' || $layout[$y][$z] === '.') {
                    $freeSeatsCounter++;
                }
            }
        }

        if ($freeSeatsCounter === 8) {
            return '#';
        }

        return $layout[$i][$j];
    }

    private function printLayout(
        array $seatLayout
    ) {
        foreach ($seatLayout as $line) {
            foreach ($line as $position) {
                $this->output->write($position);
            }
            $this->output->writeln('');
        }
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $seatLayout = $this->getLayout();
        $start      = microtime(true);
        $this->part1($seatLayout);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
