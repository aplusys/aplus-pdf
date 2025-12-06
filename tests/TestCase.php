<?php

namespace Aplus\Snappy\Tests;

use Aplus\Snappy\SnappyServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SnappyServiceProvider::class,
        ];
    }
}
