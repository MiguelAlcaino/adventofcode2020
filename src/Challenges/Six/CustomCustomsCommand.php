<?php

namespace AdventOfCode\Challenges\Six;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomCustomsCommand extends Command
{
    protected static       $defaultName = 'adventofcode:6';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 6 "Custom Customs"');
    }

    private function getGroups(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
    }

    private function part1(array $groups, OutputInterface $output): void
    {
        $sum = 0;
        foreach ($groups as $group) {
            $sum = $this->countAnswersOfGroup($group) + $sum;
        }

        $output->writeln(sprintf('The sum of questions is: %d', $sum));
    }

    private function countAnswersOfGroup(string $group)
    {
        $group             = str_replace("\n", '', $group);
        $arrayOfCharacters = str_split($group);

        $questions = [];
        foreach ($arrayOfCharacters as $character) {
            $questions[$character] = array_key_exists($character, $questions) ? $questions[$character] + 1 : 1;
        }

        return count($questions);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $groups = $this->getGroups();

        $start = microtime(true);
        $this->part1($groups, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
