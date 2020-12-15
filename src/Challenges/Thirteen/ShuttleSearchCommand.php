<?php

namespace AdventOfCode\Challenges\Thirteen;

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

    private function part1(array $lines)
    {
        $minimumTime = (int)$lines[0];
        $buses       = array_map(
            function (string $value) {
                return (int)$value;
            },
            array_filter(
                explode(',', $lines[1]),
                function (string $val) {
                    return $val !== 'x';
                }
            )
        );

        sort($buses);

        $waits = [];
        foreach ($buses as $busId) {
            $waits[$busId] = $busId - ($minimumTime % $busId);
        }

        asort($waits);
        $firstKey = array_key_first($waits);
        $this->output->writeln(sprintf('Result part 1: %d', ($minimumTime + $waits[$firstKey] - $minimumTime) * $firstKey));
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $lines = $this->getLines();
        $start = microtime(true);
        $this->part1($lines);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
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
