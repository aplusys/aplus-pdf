<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Drivers\BrowsershotDriver;
use Illuminate\Contracts\View\Factory;
use Spatie\Browsershot\Browsershot;
use Mockery;

class BrowsershotDriverTest extends TestCase
{
    public function testItInitializesWithConfig()
    {
        $viewFactory = Mockery::mock(Factory::class);
        $browsershot = Mockery::mock(Browsershot::class);
        
        $browsershot->shouldReceive('setNodeBinary')->with('/path/to/node')->once();
        
        new BrowsershotDriver($viewFactory, ['node_binary' => '/path/to/node'], $browsershot);
        
        $this->assertTrue(true);
    }
    
    public function testRenderSetsHtml()
    {
        $viewFactory = Mockery::mock(Factory::class);
        $browsershot = Mockery::mock(Browsershot::class);
        
        $driver = new BrowsershotDriver($viewFactory, [], $browsershot);
        
        $browsershot->shouldReceive('setHtml')->with('<h1>Test</h1>')->once();
        $browsershot->shouldReceive('pdf')->andReturn('PDF_CONTENT')->once();
        
        $driver->render('<h1>Test</h1>');
        
        $this->assertTrue(true);
    }
    
    public function testRenderOptionsAreApplied()
    {
        $viewFactory = Mockery::mock(Factory::class);
        $browsershot = Mockery::mock(Browsershot::class);
        
        $driver = new BrowsershotDriver($viewFactory, [], $browsershot);
        
        $browsershot->shouldReceive('setHtml')->once();
        // landscape() is a method on Browsershot
        $browsershot->shouldReceive('landscape')->once();
        // unknown option goes to setOption
        $browsershot->shouldReceive('setOption')->with('foo', 'bar')->once();
        $browsershot->shouldReceive('pdf')->andReturn('PDF_CONTENT')->once();

        $driver->render('html', [
            'landscape' => true,
            'foo' => 'bar'
        ]);
        
        $this->assertTrue(true);
    }
}
