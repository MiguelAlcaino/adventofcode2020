<?php

namespace AdventOfCode\Challenges\Four;

use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PassportProcessing extends Command
{
    protected static $defaultName = 'adventofcode:4';

    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 4 "Passport Processing"');
    }

    private function getMap(): string
    {
        return $this->inputExtractor->getContent(__DIR__);
    }

    private function part1(string $content, OutputInterface $output)
    {
        $passports      = preg_split("/(\n\n)/", $content);
        $validPassports = array_filter(
            $passports,
            function (string $passport) {
                $isValid = (
                    (false !== strpos($passport, 'byr:')) and
                    (false !== strpos($passport, 'iyr:')) and
                    (false !== strpos($passport, 'eyr:')) and
                    (false !== strpos($passport, 'hgt:')) and
                    (false !== strpos($passport, 'hcl:')) and
                    (false !== strpos($passport, 'ecl:')) and
                    (false !== strpos($passport, 'pid:'))
                ) or
                false !== strpos($passport, 'cid:');

                return $isValid;
            }
        );

        $output->writeln(sprintf('%d passports are valid', count($validPassports)));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = $this->getMap();

        $start = microtime(true);
        $this->part1($content, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
