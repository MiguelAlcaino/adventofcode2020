<?php

namespace AdventOfCode\Challenges\Two\Service;

class PasswordFilterService
{
    public function filterPasswordsByOldAlgorithm(array $lines): array
    {
        return array_filter(
            $lines,
            function (string $line) {
                $values    = preg_split("/(\s|:\s)/", $line);
                $minAndMax = explode('-', $values[0]);
                $charCount = substr_count($values[2], $values[1]);

                return $charCount >= (int)$minAndMax[0] && $charCount <= (int)$minAndMax[1];
            }
        );
    }

    public function filterPasswordsByNewAlgorithm(array $lines): array
    {
        return array_filter(
            $lines,
            function (string $line) {
                $values      = preg_split("/(\s|:\s)/", $line);
                $positions   = explode('-', $values[0]);
                $splitString = str_split($values[2]);

                return $splitString[$positions[0] - 1] === $values[1] xor $splitString[$positions[1] - 1] === $values[1];
            }
        );
    }
}
