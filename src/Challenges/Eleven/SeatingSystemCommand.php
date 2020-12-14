<?php

namespace AdventOfCode\Challenges\Eleven;

use AdventOfCode\Tools\InputExtractor;
use Closure;
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

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $seatLayout = $this->getLayout();
        $start      = microtime(true);
        $this->part1($seatLayout);
        $this->part2($seatLayout);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
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

    private function part2(array $seatLayout)
    {
        $this->printLayout($seatLayout);
        $arePreviousAndNewLayoutEquals = false;
        $loopCounter                   = 0;
        while (!$arePreviousAndNewLayoutEquals) {
            $newLayout = $this->processSeatingOrLeavingTwo($seatLayout);
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

    private function processSeatingOrLeavingTwo(array $currentStateOfSeatingLayout)
    {
        $newLayout = $currentStateOfSeatingLayout;
        for ($i = 0; $i < count($currentStateOfSeatingLayout); $i++) {
            for ($j = 0; $j < count($currentStateOfSeatingLayout[$i]); $j++) {
                $isSeatFree = $this->isSeatFree($i, $j, $currentStateOfSeatingLayout);
                if ($isSeatFree === true) {
                    $newLayout[$i][$j] = $this->occupySeatIfPossibleTwo($i, $j, $currentStateOfSeatingLayout);
                } elseif ($isSeatFree === false) {
                    $newLayout[$i][$j] = $this->freeSeatIfPossibleTwo($i, $j, $currentStateOfSeatingLayout);
                }
            }
        }

        return $newLayout;
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

    private function freeSeatIfPossibleTwo(int $i, int $j, array $layout)
    {
        $occupiedSeatCounter = 0;
        for ($y = $i - 1; $y < $i + 2; $y++) {
            for ($z = $j - 1; $z < $j + 2; $z++) {
                // Same position
                if ($y === $i && $z === $j) {
                    continue;
                }

                // Explore the diagonals
                $diffY = $y - $i;
                $diffZ = $z - $j;

                $this->walkDiagonalForUsedSeats(
                    $occupiedSeatCounter,
                    $this->getDirectionWhereToWalk($diffY, $diffZ),
                    $y,
                    $z,
                    $layout
                );

                if ($occupiedSeatCounter >= 5) {
                    return 'L';
                }
            }
        }

        return $layout[$i][$j];
    }

    private function occupySeatIfPossibleTwo(
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

                // Explore the diagonals
                $diffY = $y - $i;
                $diffZ = $z - $j;

                $result = $this->walkDiagonal(
                    $freeSeatsCounter,
                    $this->getDirectionWhereToWalk($diffY, $diffZ),
                    $y,
                    $z,
                    $i,
                    $j,
                    $layout
                );
                if ($result !== true) {
                    return $result;
                }
            }
        }

        if ($freeSeatsCounter === 8) {
            return '#';
        }

        return $layout[$i][$j];
    }

    /**
     * @return Closure
     */
    private function getDirectionWhereToWalk(int $diffY, int $diffZ)
    {
        if ($diffY < 0 && $diffZ < 0) { // top left
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY--;
                $newZ--;
            };
        } elseif ($diffY < 0 && $diffZ > 0) { // top right
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY--;
                $newZ++;
            };
        } elseif ($diffY > 0 && $diffZ > 0) { // bottom right
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY++;
                $newZ++;
            };
        } elseif ($diffY > 0 && $diffZ < 0) { // bottom left
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY++;
                $newZ--;
            };
        } elseif ($diffY === 0 && $diffZ < 0) { // left
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newZ--;
            };
        } elseif ($diffY === 0 && $diffZ > 0) { // right
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newZ++;
            };
        } elseif ($diffZ === 0 && $diffY < 0) { // top
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY--;
            };
        } elseif ($diffZ === 0 && $diffY > 0) { // bottom
            $modifyIndexes = function (int &$newY, int &$newZ) {
                $newY++;
            };
        }

        return $modifyIndexes;
    }

    /**
     * Returns true if objective found. Or returns a value if not
     *
     * @return bool|string
     */
    private function walkDiagonal(
        int &$freeSeatsCounter,
        callable $modifyIndexes,
        int $newY,
        int $newZ,
        int $i,
        int $j,
        array $layout
    ) {
        while (true) {
            // If free seat is found
            if ($this->incrementFreeSeatsCounter($freeSeatsCounter, $newY, $newZ, $layout)) {
                return true;
            }

            if ($layout[$newY][$newZ] === '.') {
                $modifyIndexes($newY, $newZ);
                continue;
            }

            if ($layout[$newY][$newZ] === '#') {
                return $layout[$i][$j];
            }
        }
    }

    private function walkDiagonalForUsedSeats(
        int &$usedSeatsCounter,
        callable $modifyIndexes,
        int $y,
        int $z,
        array $layout
    ) {
        while (true) {
            // If position doesn't exist, stop looping
            if (!isset($layout[$y][$z]) || $layout[$y][$z] === 'L') {
                break;
            }

            if ($layout[$y][$z] === '.') {
                $modifyIndexes($y, $z);
                continue;
            }

            if ($layout[$y][$z] === '#') {
                $usedSeatsCounter++;
                break;
            }
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

                if ($this->incrementFreeSeatsCounter($freeSeatsCounter, $y, $z, $layout, true)) {
                    continue;
                }

                return $layout[$i][$j];
            }
        }

        if ($freeSeatsCounter === 8) {
            return '#';
        }

        return $layout[$i][$j];
    }

    private function incrementFreeSeatsCounter(int &$counter, int $y, int $z, array $layout, bool $checkDot = false)
    {
        if (!isset($layout[$y][$z]) || $layout[$y][$z] === 'L' || ($checkDot ? $layout[$y][$z] === '.' : false)) {
            $counter++;

            return true;
        }

        return false;
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
}
