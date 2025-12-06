<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Builders\PdfBuilder;
use Aplus\Pdf\Contracts\DriverInterface;
use Mockery;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class PdfBuilderTest extends OrchestraTestCase
{
    public function test_it_can_render_html()
    {
        $driver = Mockery::mock(DriverInterface::class);
        $driver->shouldReceive('render')
            ->once()
            ->with('<h1>Hello</h1>', ['foo' => 'bar'])
            ->andReturn('PDF_CONTENT');

        $builder = new PdfBuilder($driver);
        
        $result = $builder->html('<h1>Hello</h1>')
            ->option('foo', 'bar')
            ->output();

        $this->assertEquals('PDF_CONTENT', $result);
    }

    public function test_it_can_render_url()
    {
        $driver = Mockery::mock(DriverInterface::class);
        $driver->shouldReceive('renderFromUrl')
            ->once()
            ->with('http://example.com', [])
            ->andReturn('PDF_FROM_URL');

        $builder = new PdfBuilder($driver);
        
        $result = $builder->url('http://example.com')
            ->output();

        $this->assertEquals('PDF_FROM_URL', $result);
    }
}
