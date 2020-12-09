<?php

namespace AdventOfCode\Challenges\Seven;

use AdventOfCode\Challenges\Seven\Model\Bag;
use AdventOfCode\Tools\InputExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandyHaversacksCommands extends Command
{
    protected static       $defaultName = 'adventofcode:7';
    private InputExtractor $inputExtractor;

    public function __construct()
    {
        $this->inputExtractor = new InputExtractor();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Advent of Code: Challenge day 7 "Handy Haversacks"');
    }

    private function getBagRules(): array
    {
        $content = $this->inputExtractor->getContent(__DIR__);

        return array_filter(
            explode("\n", $content),
            function (string $val) {
                return !empty($val);
            }
        );
    }

    private function part2(array $bagRules, OutputInterface $output)
    {
        $bagTree = $this->getPlainBagTree($bagRules);

        $amountOfBags = $this->countABag('shiny gold', 0, $bagTree);
        $output->writeln(sprintf('The amount of bags contained in shiny gold is: %d', $amountOfBags));
    }

    private function countABag(string $color, int $amount, array $bagTree)
    {
        if (count($bagTree[$color]) === 0) {
            return $amount;
        }

        $sum = 0;
        foreach ($bagTree[$color] as $indexColor => $indexAmount) {
            $sum = $this->countABag($indexColor, $indexAmount, $bagTree) + $sum;
        }

        if ($amount === 0) {
            return $sum;
        }

        return $amount + $amount * $sum;
    }

    private function part1(array $bagRules, OutputInterface $output)
    {
        $bagTree = $this->getBagTree($bagRules);
        $count   = 0;
        foreach ($bagTree as $node) {
            $has = $this->hasShinyBag($node->getChildren(), $bagTree);
            if ($has) {
                $count++;
            }
        }

        $output->writeln(sprintf('Count: %d', $count));
    }

    private function printFromBagRules(array $bagRules, OutputInterface $output)
    {
        $bagTree = $this->getBagTree($bagRules);

        foreach ($bagTree as $bag) {
            $childrenString = '';
            if (count($bag->getChildren()) === 0) {
                $childrenString = ' no other bags';
            } else {
                foreach ($bag->getChildren() as $child) {
                    $childrenString .= sprintf(' %d %s %s,', $child->getAmount(), $child->getColor(), $child->getAmount() === 1 ? 'bag' : 'bags');
                }
                $childrenString = substr($childrenString, 0, -1);
            }

            $output->writeln(sprintf('%s bags contain%s.', $bag->getColor(), $childrenString));
        }
    }

    /**
     * @param Bag[] $bagTree
     *
     * @return bool
     */
    private function hasShinyBag(array $children, array $bagTree)
    {
        $children = $this->getConnectedChildren($children, $bagTree);
        if (count($children) === 0) {
            return false;
        }

        /** @var Bag $node */
        $node = array_shift($children);

        return $node->getColor() === 'shiny gold' || $this->hasShinyBag($node->getChildren(), $bagTree) || $this->hasShinyBag($children, $bagTree);
    }

    /**
     * @param Bag[] $nodes
     *
     * @return Bag[]
     */
    public function getConnectedChildren(array $children, array $bagTree): array
    {
        $completeNodes = [];
        foreach ($children as $node) {
            $completeNodes[$node->getColor()] = $bagTree[$node->getColor()];
        }

        return $completeNodes;
    }

    /**
     * @param array $bagRules
     *
     * @return Bag[]
     */
    private function getBagTree(array $bagRules): array
    {
        $bagRuleInstances = [];
        foreach ($bagRules as $bagRule) {
            $colorAndContainment = explode(' bags contain ', $bagRule);
            $bag                 = new Bag($colorAndContainment[0]);
            $matches             = [];
            preg_match_all("/(\d)\s(\w+\s\w+)/", $colorAndContainment[1], $matches);

            if (count($matches[1]) > 0) {
                foreach ($matches[1] as $key => $amount) {
                    $bag->addNode(new Bag($matches[2][$key], (int)$amount));
                }
            }
            $bagRuleInstances[$bag->getColor()] = $bag;
        }

        return $bagRuleInstances;
    }

    private function getPlainBagTree(array $bagRules): array
    {
        $bagRuleInstances = [];
        foreach ($bagRules as $bagRule) {
            $colorAndContainment = explode(' bags contain ', $bagRule);

            $matches = [];
            preg_match_all("/(\d)\s(\w+\s\w+)/", $colorAndContainment[1], $matches);

            $children = [];
            if (count($matches[1]) > 0) {
                foreach ($matches[1] as $key => $amount) {
                    $children[$matches[2][$key]] = (int)$amount;
                }
            }
            $bagRuleInstances[$colorAndContainment[0]] = $children;
        }

        return $bagRuleInstances;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bagRules = $this->getBagRules();

        $start = microtime(true);
        $this->part1($bagRules, $output);
        $this->part2($bagRules, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
