<?php

namespace AdventOfCode\Challenges\Fourteen;

use AdventOfCode\Challenges\Fourteen\Model\Group;
use AdventOfCode\Challenges\Fourteen\Model\MemAddress;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockingDataCommand extends Command
{
    protected static $defaultName = 'adventofcode:14';
    private InputExtractor  $inputExtractor;
    private OutputInterface $output;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->output = $output;
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 14 "Docking Data"');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $groups = $this->getGroups();
        $start = microtime(true);
        $this->part1($groups);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }

    /**
     * @param Group[] $groups
     */
    private function part1(array $groups)
    {
        $mem = [];
        foreach ($groups as $group) {
            $this->applyMaskToGroup($group, $mem);
        }

        $this->output->writeln(sprintf('Part 1 sum: %d', array_sum($mem)));
    }

    private function applyMaskToGroup(Group $group, array &$mem)
    {
        foreach ($group->getMemAddresses() as $memAddress) {
            $number = $this->applyMask($group->getMask(), $memAddress->getOriginalValue());
            $memAddress->setNewValue($number);
            $mem[$memAddress->getAddress()] = $number;
        }
    }

    private function applyMask(string $mask, int $number): int
    {
        $decimalNumber = decbin($number);
        $paddedBinary = str_pad($decimalNumber, 36, '0', STR_PAD_LEFT);

        $splitMask = str_split($mask);
        $splitBinary = str_split($paddedBinary);

        foreach ($splitMask as $key => $unitMask) {
            if ($unitMask !== 'X') {
                $splitBinary[$key] = $unitMask;
            }
        }

        return bindec(implode('', $splitBinary));
    }

    private function getGroups(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        $lines = array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
        /** @var Group[] $groups */
        $groups = [];
        /** @var Group $group */
        $group = null;
        foreach ($lines as $line) {
            if (strpos($line, 'mask = ') !== false) {
                $mask = explode('mask = ', $line);
                $group = new Group($mask[1]);
                $groups[] = $group;
            } elseif (strpos($line, 'mem') !== false) {
                preg_match("/mem\[(\d+)\]\s=\s(\d+)/", $line, $matches);
                $group->addMemAddress(new MemAddress((int)$matches[1], (int)$matches[2]));
            }
        }

        return $groups;
    }
}
