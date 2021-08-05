<?php

namespace Lukasss93\LarexCrowdin\Tests;

use Lukasss93\Larex\LarexServiceProvider;
use Lukasss93\LarexCrowdin\LarexCrowdinServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LarexServiceProvider::class,
            LarexCrowdinServiceProvider::class,
        ];
    }
}
