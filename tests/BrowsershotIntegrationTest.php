<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Drivers\BrowsershotDriver;
use Aplus\Pdf\PdfManager;
use Aplus\Pdf\Facades\Apdf;

class BrowsershotIntegrationTest extends TestCase
{
    public function testManagerResolvesBrowsershotDriver()
    {
        // Set default driver to browsershot
        config(['aplus-pdf.default' => 'browsershot']);
        
        $driver = Apdf::driver('browsershot');
        
        $this->assertInstanceOf(BrowsershotDriver::class, $driver);
    }
    
    public function testFacadeResolvesToBrowsershotWhenDefault()
    {
        config(['aplus-pdf.default' => 'browsershot']);
        
        // When accessing Facade methods, it should proxy to default driver
        // We can check the underlying instance by resolving the manager
        
        $manager = app('aplus.pdf');
        $this->assertInstanceOf(BrowsershotDriver::class, $manager->driver());
    }
}
