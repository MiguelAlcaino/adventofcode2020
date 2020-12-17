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

        usort(
            $buses,
            function (Bus $a, Bus $b) {
                return $a->getId() > $b->getId() ? -1 : 1;
            }
        );

        $first  = array_shift($buses);
        $second = array_shift($buses);
        $a      = 1;
        while (true) {
            $divisor = $second->getId() * $a - $second->getOffset() + $first->getOffset();
            if ($divisor % $first->getId() === 0) {
                $d = $divisor / $first->getId();
                $t = ($d * $first->getId()) - $first->getOffset();

                $found = true;
                $this->output->writeln(sprintf('t:%d', $t));
                foreach ($buses as $bus) {
                    if (($t + $bus->getOffset()) % $bus->getId() !== 0) {
                        $found = false;
                        break;
                    }
                }

                if ($found) {
                    $this->output->writeln(sprintf('final t:%d', $t));

                    return;
                }
            }
            $a++;
        }

        // while (true) {
        //     $divisor = (1789 * $a + 3);
        //     if ($divisor % 1889 === 0) {
        //         $d = $divisor / 1889;
        //         $t = ($d * 1889) - 3;
        //         $this->output->writeln(sprintf('t:%d', $t));
        //         $hola = 1;
        //         if (($t + 2) % 47 == 0 && ($t + 1) % 37 === 0 && $t % 1789 === 0) {
        //             $ganamos = true;
        //         }
        //     }
        //     $a++;
        // }
        //
        // $previousShuttle = array_shift($buses);
        //
        // $i             = 1;
        // $hasTCandidate = true;
        // // $t             = $previousShuttle->getId() * $i;
        // foreach ($buses as $bus) {
        //     while (true) {
        //         $t = $previousShuttle->getId() * $i;
        //         $f = $t + $bus->getOffset();
        //         if ($f % $bus->getId() === 0) {
        //             $this->output->writeln(sprintf('t=%d with i=%d', $t, $i));
        //         }
        //         $i++;
        //     }
        // }
        // foreach ($buses as $bus) {
        //     while (true) {
        //         if (!$hasTCandidate) {
        //             $t = $previousShuttle->getId() * $i;
        //         }
        //         $f = $t + $bus->getOffset();
        //         if ($f % $bus->getId() === 0) {
        //             $i             = $t;
        //             $hasTCandidate = true;
        //             break;
        //         } else {
        //             $hasTCandidate = false;
        //         }
        //
        //         $i++;
        //     }
        // }
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
