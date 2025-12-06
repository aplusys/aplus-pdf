<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Pdf;
use Aplus\Pdf\Image;
use Aplus\Pdf\Contracts\DriverInterface;
use Aplus\Pdf\PdfManager;
use Mockery;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ViewLoadingTest extends TestCase
{
    public function testPdfLoadView()
    {
        $viewFactory = Mockery::mock(Factory::class);
        $view = Mockery::mock(View::class);
        $driver = Mockery::mock(DriverInterface::class);
        
        $viewFactory->shouldReceive('make')->never(); 
        // The Driver is responsible for loading the view, so correct expectation:
        $driver->shouldReceive('loadView')->with('test.view', ['foo' => 'bar'])->once()->andReturnSelf();

        // Bind manager to return our mock driver
        $this->app->bind('aplus.pdf', function () use ($driver) {
             return $driver;
        });
        
        // This test is slightly different now as Manager resolves to Driver
        // We are testing if Facade calls Driver correcty.
        
        \Aplus\Pdf\Facades\Pdf::loadView('test.view', ['foo' => 'bar']);
        
        $this->assertTrue(true);
     }

    public function testImageLoadView()
    {
        $viewFactory = Mockery::mock(Factory::class);
        $view = Mockery::mock(View::class);

        $viewFactory->shouldReceive('make')
            ->once()
            ->with('test.image', ['baz' => 'qux'], [])
            ->andReturn($view);
        
        $view->shouldReceive('render')
            ->once()
            ->andReturn('<img src="test.jpg">');

        $image = new Image();
        $image->setViewFactory($viewFactory);

        $image->view('test.image', ['baz' => 'qux']);

        // Reflection to check protected html property
        $reflection = new \ReflectionClass($image);
        $property = $reflection->getProperty('html');
        $property->setAccessible(true);
        
        $this->assertEquals('<img src="test.jpg">', $property->getValue($image));
    }

    public function testPdfDownloadUseHtmlState()
    {
        $pdf = Mockery::mock(Pdf::class . '[getOutputFromHtml]'); // Partial mock
        $pdf->loadHTML('<h1>State Content</h1>');
        
        $pdf->shouldReceive('getOutputFromHtml')
            ->once()
            ->with('<h1>State Content</h1>')
            ->andReturn('PDF BINARY DATA');

        $response = $pdf->download();
        
        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
        $this->assertEquals('PDF BINARY DATA', $response->getContent());
    }

    public function testLoadHtmlWithRenderable()
    {
        $renderable = Mockery::mock(\Illuminate\Contracts\Support\Renderable::class);
        $renderable->shouldReceive('render')->once()->andReturn('<h1>Renderable HTML</h1>');

        $pdf = new Pdf();
        $pdf->loadHTML($renderable);

        // Reflection to check protected html property
        $reflection = new \ReflectionClass($pdf);
        $property = $reflection->getProperty('html');
        $property->setAccessible(true);
        
        $this->assertEquals('<h1>Renderable HTML</h1>', $property->getValue($pdf));
    }
}
