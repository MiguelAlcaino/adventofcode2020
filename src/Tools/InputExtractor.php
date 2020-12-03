<?php

namespace AdventOfCode\Tools;

use Symfony\Component\Finder\Finder;

class InputExtractor
{
    private Finder $finder;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function getContent(string $dir): string
    {
        $finder = new Finder();
        // find all files in the current directory
        $files = $finder->files()->in($dir . '/Resources/');
        $files->name('input.txt');
        foreach ($files->getIterator() as $fileInfo) {
            return $content = $fileInfo->getContents();
        }
    }
}
