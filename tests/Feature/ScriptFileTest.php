<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ScriptFileTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function can_only_enter_one_argument_after_script_file($command, $expect)
    {
        $terminal = shell_exec($command);

        $this->assertEquals($expect, $terminal);
    }

    public function dataProvider()
    {
        return [
                [
                    'php script.php',
                    "Enter only 1 argument after script.php\n",
                ],
                [
                    'php script.php asd asd',
                    "Enter only 1 argument after script.php\n",
                ],
                [
                    'php script.php file.txt',
                    'File format must be ".csv"'."\n",
                ],
                [
                    'php script.php file.csv',
                    'File not found'."\n",
                ],
            ];
    }
}
