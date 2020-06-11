<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\FeesController;
use PHPUnit\Framework\TestCase;

class FeeControllerUnitTest extends TestCase
{
    /**
     * @test
     * @dataProvider data_provider
     */
    public function has_access_to_different_classes(string $expected, string $method)
    {
        $app = new FeesController();
        $string = 'getUsersInstance';
        $this->assertInstanceOf($expected, $app->$method());
    }

    public function data_provider()
    {
        return [
                [
                    'App\Model\Users',
                    'getUsersInstance',
                ],
                [
                    'App\Support\Files\CsvReader',
                    'getFilesInstance',
                ],
                [
                    'App\Support\Calculations\Fees',
                    'getFeesInstance',
                ],
            ];
    }
}
