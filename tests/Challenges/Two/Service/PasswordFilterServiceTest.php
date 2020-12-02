<?php

namespace AdventOfCode\Tests\Challenges\Two\Service;

use AdventOfCode\Challenges\Two\Service\PasswordFilterService;
use PHPUnit\Framework\TestCase;

class PasswordFilterServiceTest extends TestCase
{
    public function testFilterPasswordsByNewAlgorithm()
    {
        $passwordService   = new PasswordFilterService();
        $filteredPasswords = $passwordService->filterPasswordsByNewAlgorithm(
            [
                '1-3 a: abcde', // valid
                '1-3 b: cdefg', //invalid
                '2-9 c: ccccccccc' //invalid
            ]
        );

        self::assertCount(1, $filteredPasswords);
    }
}
