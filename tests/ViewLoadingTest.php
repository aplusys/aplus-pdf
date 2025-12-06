<?php

namespace Aplus\Snappy\Tests;

use Aplus\Snappy\Pdf;
use Aplus\Snappy\Image;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;
use Mockery;

class ViewLoadingTest extends TestCase
{
    public function testPdfLoadView()
    {
        $viewFactory = Mockery::mock(ViewFactory::class);
        $view = Mockery::mock(View::class);

        $viewFactory->shouldReceive('make')
            ->once()
            ->with('test.view', ['foo' => 'bar'], [])
            ->andReturn($view);
        
        $view->shouldReceive('render')
            ->once()
            ->andReturn('<h1>Test HTML</h1>');

        $pdf = new Pdf(); // Instantiated manually to inject mock
        $pdf->setViewFactory($viewFactory);

        $pdf->loadView('test.view', ['foo' => 'bar']);
        
        // Reflection to check protected html property
        $reflection = new \ReflectionClass($pdf);
        $property = $reflection->getProperty('html');
        $property->setAccessible(true);
        
        $this->assertEquals('<h1>Test HTML</h1>', $property->getValue($pdf));
    }

    public function testImageLoadView()
    {
        $viewFactory = Mockery::mock(ViewFactory::class);
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
