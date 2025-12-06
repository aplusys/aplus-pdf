<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\SnappyServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SnappyServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('snappy.drivers.wkhtmltopdf.binary', '/usr/bin/wkhtmltopdf');
        $app['config']->set('snappy.drivers.wkhtmltopdf.timeout', false);
        $app['config']->set('snappy.drivers.wkhtmltopdf.options', [
            'margin-top'    => 0,
            'margin-right'  => 0,
            'margin-bottom' => 0,
            'margin-left'   => 0,
            'page-size'     => 'a4',
            'orientation'   => 'portrait',
        ]);
        $app['config']->set('snappy.drivers.wkhtmltopdf.env', []);
        
        $app['config']->set('snappy.image.binary', '/usr/local/bin/wkhtmltoimage');
        $app['config']->set('snappy.image.timeout', false);
        $app['config']->set('snappy.image.options', []);
        $app['config']->set('snappy.image.env', []);
    }
}
