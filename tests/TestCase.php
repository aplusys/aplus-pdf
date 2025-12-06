<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\PdfServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PdfServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('aplus-pdf.drivers.wkhtmltopdf.binary', '/usr/bin/wkhtmltopdf');
        $app['config']->set('aplus-pdf.drivers.wkhtmltopdf.timeout', false);
        $app['config']->set('aplus-pdf.drivers.wkhtmltopdf.options', [
            'margin-top'    => 0,
            'margin-right'  => 0,
            'margin-bottom' => 0,
            'margin-left'   => 0,
            'page-size'     => 'a4',
            'orientation'   => 'portrait',
        ]);
        $app['config']->set('aplus-pdf.drivers.wkhtmltopdf.env', []);
        
        $app['config']->set('aplus-pdf.image.binary', '/usr/local/bin/wkhtmltoimage');
        $app['config']->set('aplus-pdf.image.timeout', false);
        $app['config']->set('aplus-pdf.image.options', []);
        $app['config']->set('aplus-pdf.image.env', []);
    }
}
