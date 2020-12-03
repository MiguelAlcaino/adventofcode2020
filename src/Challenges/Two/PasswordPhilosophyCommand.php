<?php

namespace AdventOfCode\Challenges\Two;

use AdventOfCode\Challenges\Two\Service\PasswordFilterService;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PasswordPhilosophyCommand extends Command
{
    protected static $defaultName = 'adventofcode:2';

    private PasswordFilterService $passwordFilterService;
    private InputExtractor        $inputExtractor;

    public function __construct()
    {
        $this->passwordFilterService = new PasswordFilterService();
        $this->inputExtractor        = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 2 "Password Philosophy"');
    }

    private function getLines(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n", $content),
            function ($val) {
                return !empty($val);
            }
        );
    }

    private function part1(array $lines, OutputInterface $output)
    {
        $filteredArray = $this->passwordFilterService->filterPasswordsByOldAlgorithm($lines);

        $output->writeln(sprintf('%d passwords are correct with the old algorithm', count($filteredArray)));
    }

    private function part2(array $lines, OutputInterface $output)
    {
        $filteredArray = $this->passwordFilterService->filterPasswordsByNewAlgorithm($lines);

        $output->writeln(sprintf('%d passwords are correct with the new algorithm', count($filteredArray)));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lines = $this->getLines();

        $start = microtime(true);
        $this->part1($lines, $output);
        $this->part2($lines, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
