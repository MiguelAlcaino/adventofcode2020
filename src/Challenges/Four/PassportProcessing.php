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

    private function getPassports(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return preg_split("/(\n\n)/", $content);
    }

    private function checkHairColor(string $value)
    {
        return 0 !== preg_match("/#[0-9a-fA-F]{6}+/", $value);
    }

    private function part1(array $passports, OutputInterface $output): array
    {
        $validPassports = array_filter(
            $passports,
            function (string $passport) {
                return
                    (false !== strpos($passport, 'byr:'))
                    && (false !== strpos($passport, 'iyr:'))
                    && (false !== strpos($passport, 'eyr:'))
                    && (false !== strpos($passport, 'hgt:'))
                    && (false !== strpos($passport, 'hcl:'))
                    && (false !== strpos($passport, 'ecl:'))
                    && (false !== strpos($passport, 'pid:'));
            }
        );

        $output->writeln(sprintf('%d passports are valid', count($validPassports)));

        return $validPassports;
    }

    private function part2(array $passports, OutputInterface $output)
    {
        $validPassports = array_filter(
            $passports,
            function (string $passport) {
                $fieldsAndValues = preg_split("/(\s)/", $passport);
                $fieldsAndValues = array_map(
                    function (string $pair) {
                        return explode(':', $pair);
                    },
                    $fieldsAndValues
                );

                $previousIsValid = true;
                foreach ($fieldsAndValues as $fieldAndValue) {
                    if (!$previousIsValid) {
                        break;
                    }
                    $field = $fieldAndValue[0];
                    // Shitty fix for last line. This should be fixed in the preg_split done at the beggining of this funciton
                    if (!isset($fieldAndValue[1])) {
                        break;
                    }
                    $value = $fieldAndValue[1];
                    switch ($field) {
                        case 'byr':
                            $year    = (int)$value;
                            $isValid = (strlen($value) === 4) && ($year >= 1920) && ($year <= 2002);
                            break;
                        case 'iyr':
                            $year    = (int)$value;
                            $isValid = (strlen($value) === 4) && ($year >= 2010) && ($year <= 2020);
                            break;
                        case 'eyr':
                            $year    = (int)$value;
                            $isValid = (strlen($value) === 4) && ($year >= 2020) && ($year <= 2030);
                            break;
                        case 'hgt':
                            if (strpos($value, 'cm') !== false) {
                                $height  = (int)str_replace('cm', '', $value);
                                $isValid = $height >= 150 && $height <= 193;
                            } elseif (strpos($value, 'in') !== false) {
                                $height  = (int)str_replace('in', '', $value);
                                $isValid = $height >= 59 && $height <= 76;
                            } else {
                                $isValid = false;
                            }
                            break;
                        case 'hcl':
                            $isValid = $this->checkHairColor($value);
                            break;
                        case 'ecl':
                            $isValid =
                                $value === 'amb'
                                || $value === 'blu'
                                || $value === 'brn'
                                || $value === 'gry'
                                || $value === 'grn'
                                || $value === 'hzl'
                                || $value === 'oth';
                            break;
                        case 'pid':
                            $isValid = strlen($value) === 9 && is_numeric($value);
                            break;
                        case 'cid':
                            $isValid = true;
                            break;
                    }
                    $previousIsValid = $isValid && $previousIsValid;
                }

                return $previousIsValid;
            }
        );
        $output->writeln(sprintf('There are %d valid passports for part 2', count($validPassports)));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passports = $this->getPassports();

        $start          = microtime(true);
        $validPassports = $this->part1($passports, $output);
        $this->part2($validPassports, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
