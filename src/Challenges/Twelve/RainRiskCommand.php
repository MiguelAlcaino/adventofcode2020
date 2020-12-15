<?php

namespace AdventOfCode\Challenges\Twelve;

use AdventOfCode\Challenges\Twelve\Model\Movement;
use AdventOfCode\Challenges\Twelve\Model\Ship;
use AdventOfCode\Challenges\Twelve\Model\Waypoint;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RainRiskCommand extends Command
{
    protected static        $defaultName = 'adventofcode:12';
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
        $this->setDescription('Advent of Code: Challenge day 12 "Rain Risk"');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $movements = $this->getMovements();
        $start     = microtime(true);
        $this->part1($movements);
        $this->part2($movements);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }

    private function getMovements(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_map(
            function (string $val) {
                preg_match("/([NSEWLFR])(\d+)/", $val, $matches);

                return new Movement($matches[1], (int)$matches[2]);
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
     * @param Movement[] $movements
     */
    private function part1(array $movements)
    {
        $ship = new Ship();
        foreach ($movements as $movement) {
            $ship->move($movement);
            // $this->output->writeln(sprintf('Movement: %s%d - Ship: %s', $movement->getAction(), $movement->getValue(), $ship->getPosition()));
        }
        $this->output->writeln(sprintf('The sum of values for part 1: %d', abs($ship->getX()) + abs($ship->getY())));
    }

    /**
     * @param Movement[] $movements
     */
    private function part2(array $movements)
    {
        $ship = new Ship();
        $ship->setWaypoint(new Waypoint(10, 1));
        foreach ($movements as $movement) {
            $ship->move2($movement);
            // $this->output->writeln(sprintf('Movement: %s%d - Ship: %s - %s', $movement->getAction(), $movement->getValue(), $ship->getPosition(), $ship->getWaypoint()->getPosition()));
        }
        $this->output->writeln(sprintf('The sum of values for part 2: %d', abs($ship->getX()) + abs($ship->getY())));
    }
}
