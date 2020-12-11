<?php

namespace AdventOfCode\Challenges\Ten;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdapterArrayCommand extends Command
{
    protected static       $defaultName = 'adventofcode:10';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 10 "Adapter Array"');
    }

    private function getAdapters(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_map(
            function (string $val) {
                return (int)$val;
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
     * @param int[]           $adapters
     * @param OutputInterface $output
     */
    private function part1(array $adapters, OutputInterface $output)
    {
        asort($adapters);
        $difference    = 0;
        $previousValue = 0;
        $differences   = [];
        foreach ($adapters as $key => $adapterValue) {
            if (0 === $previousValue) {
                $previousValue = 0;
            }
            $difference = $adapterValue - $previousValue;
            if ($difference > 3) {
                throw new \Exception('The difference is bigger than 3!');
            }

            if (!array_key_exists($difference, $differences)) {
                $differences[$difference] = 1;
            } else {
                $differences[$difference]++;
            }

            $previousValue = $adapterValue;
        }

        // Connect to the device
        $differences[3]++;

        $output->writeln(sprintf('Number 1 differences (%d) multiplied by number 3 differences (%d): %d', $differences[1], $differences[3], $differences[1] * $differences[3]));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numbers = $this->getAdapters();
        $start   = microtime(true);
        $this->part1($numbers, $output);

        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
