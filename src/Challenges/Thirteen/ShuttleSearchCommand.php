<?php

namespace AdventOfCode\Challenges\Thirteen;

use AdventOfCode\Challenges\Thirteen\Model\Bus;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShuttleSearchCommand extends Command
{
    protected static        $defaultName = 'adventofcode:13';
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
        $this->setDescription('Advent of Code: Challenge day 13 "Shuttle Search"');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $lines = $this->getLines();
        $start = microtime(true);
        $this->part1($lines);
        $this->part2($lines);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }

    private function part1(array $lines)
    {
        $minimumTime = (int)$lines[0];
        $buses       = $this->getBuses($lines[1]);

        sort($buses);

        $waits = [];
        foreach ($buses as $busId) {
            $waits[$busId] = $busId - ($minimumTime % $busId);
        }

        asort($waits);
        $firstKey = array_key_first($waits);
        $this->output->writeln(sprintf('Result part 1: %d', ($minimumTime + $waits[$firstKey] - $minimumTime) * $firstKey));
    }

    private function part2(array $lines)
    {
        /** @var Bus[] $buses */
        $buses = [];
        foreach ($this->getBuses($lines[1]) as $offset => $id) {
            $buses[] = new Bus($id, $offset);
        }

        $t = 1;
        /**
         * @var int $w Offset used to make $t jump
         */
        $w = 1;
        foreach ($buses as $bus) {
            while (($bus->getOffset() + $t) % $bus->getId() !== 0) {
                $t += $w;
            }
            $w *= $bus->getId();
        }

        $this->output->writeln(sprintf('part2: t: %d', $t));
    }

    private function getBuses(string $lineWithBuses)
    {
        return array_map(
            function (string $value) {
                return (int)$value;
            },
            array_filter(
                explode(',', $lineWithBuses),
                function (string $val) {
                    return $val !== 'x';
                }
            )
        );
    }

    private function getLines(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
    }
}
