#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Finder\Finder;

/**
 * @return int[]
 */
function getNumbers(): array
{
    $finder = new Finder();
    // find all files in the current directory
    $files = $finder->files()->in(__DIR__ . '/../../resources/');
    $files->name('input.txt');
    foreach ($files->getIterator() as $fileInfo) {
        $content = $fileInfo->getContents();
    }

    $filtered = array_filter(
        explode("\n", $content),
        function ($val) {
            return is_numeric($val);
        }
    );

    return array_map(
        function (string $value) {
            return (int)$value;
        },
        $filtered
    );
}

/**
 * @param int[] $lines
 */
function part1(array $lines, OutputInterface $output): void
{
    foreach ($lines as $key => $number) {
        if ($number >= 2020) {
            continue;
        }
        foreach ($lines as $secondKey => $secondNumber) {
            if ($key === $secondKey) {
                continue;
            }

            if ($number + $secondNumber === 2020) {
                $output->writeln(sprintf('The two numbers that sum 2020 are %d and %d. And they multiply %d. Marry Christmas!', $number, $secondNumber, $number * $secondNumber));

                return;
            }
        }
    }
}

/**
 * @param int[] $lines
 */
function part2(array $lines, OutputInterface $output): void
{
    foreach ($lines as $key => $number) {
        foreach ($lines as $secondKey => $secondNumber) {
            if ($key == $secondKey || ($sumFirstAndSecond = $number + $secondNumber) > 2020) {
                continue;
            }
            foreach ($lines as $thirdKey => $thirdNumber) {
                if ($key === $secondKey && $secondKey === $thirdKey) {
                    continue;
                }

                $totalSum = $sumFirstAndSecond + $thirdNumber;
                if ($totalSum === 2020) {
                    $output->writeln(sprintf('The three numbers that sum 2020 are %d, %d and %d. And they multiply %d. Marry Christmas!', $number, $secondNumber, $thirdNumber, $number * $secondNumber * $thirdNumber));

                    return;
                }
            }
        }
    }
}

(new SingleCommandApplication())
    ->setName('My Super Command') // Optional
    ->setVersion('1.0.0') // Optional
    ->addArgument('foo', InputArgument::OPTIONAL, 'The directory')
    ->addOption('bar', null, InputOption::VALUE_REQUIRED)
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $lines = getNumbers();

            part1($lines, $output);
            part2($lines, $output);

            return Command::SUCCESS;
        }
    )
    ->run();
