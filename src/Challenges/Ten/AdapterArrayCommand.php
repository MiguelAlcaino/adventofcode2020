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

    private function part2(array $adapters, OutputInterface $output)
    {
        sort($adapters);
        array_unshift($adapters, 0); // Adding jolt output
        $adapters[] = $adapters[count($adapters) - 1] + 3; // Adding device's built-in adapter

        $adaptersGrouped = $this->separateArrayByDifferencesOfThree($adapters);

        $adaptersWithDirectChildren = [];
        $product                    = 1;

        foreach ($adaptersGrouped as $adapters) {
            foreach ($adapters as $key => $adapter) {
                $adaptersWithDirectChildren[$adapter] = $this->getClosestMatches($key, $adapters);
            }
            $sum                        = $this->countLeaves($adaptersWithDirectChildren, reset($adapters));
            $product                    = $product * $sum;
            $adaptersWithDirectChildren = [];
        }

        $output->writeln(sprintf('The usm is: %d', $product));
    }

    private function separateArrayByDifferencesOfThree(array $adapters, $min = 3)
    {
        $array      = [];
        $arrayIndex = 0;
        $group      = [];
        for ($i = 0; $i < count($adapters); $i++) {
            $group[]        = $adapters[$i];
            $arrayKeyExists = array_key_exists($i + 1, $adapters);
            if (!$arrayKeyExists || ($adapters[$i + 1] - $adapters[$i] === 3 && count($group) > $min)) {
                $array[$arrayIndex] = $group;
                $arrayIndex++;
                $group = [];
            }
        }

        return $array;
    }

    private function countLeaves(array $adaptersWithDirectChildren, int $index = 0)
    {
        if (count($adaptersWithDirectChildren[$index]) === 0) {
            return 1;
        }

        $sum = 0;
        foreach ($adaptersWithDirectChildren[$index] as $childIndex => $child) {
            $sum = $this->countLeaves($adaptersWithDirectChildren, $child) + $sum;
        }

        return $sum;
    }

    private function getClosestMatches(int $index, array $adapters)
    {
        $numberToTest      = $adapters[$index];
        $threeNextAdapters = array_slice($adapters, $index + 1, 4);
        $nextNumbers       = [];

        foreach ($threeNextAdapters as $followingAdapter) {
            if ($followingAdapter - $numberToTest <= 3) {
                $nextNumbers[] = $followingAdapter;
            }
        }

        return $nextNumbers;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numbers = $this->getAdapters();
        $start   = microtime(true);
        $this->part1($numbers, $output);
        $this->part2($numbers, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
