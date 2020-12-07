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

        if (count($children) > 0) {
            /** @var Bag $node */
            $node = array_shift($children);

            return $node->getColor() === 'shiny gold' || $this->hasShinyBag($node->getChildren(), $bagTree) || $this->hasShinyBag($children, $bagTree);
        }
    }

    /**
     * @param Bag[] $nodes
     *
     * @return Bag[]
     */
    public function getConnectedChildren(array $nodes, array $bagTree): array
    {
        $completeNodes = [];
        foreach ($nodes as $node) {
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bagRules = $this->getBagRules();

        $start = microtime(true);
        $this->part1($bagRules, $output);
        $diff = microtime(true) - $start;

        $output->writeln(sprintf('Time to calculate %s seconds', $diff));

        return Command::SUCCESS;
    }
}
