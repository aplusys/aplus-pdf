<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Facades\Pdf;
use Aplus\Pdf\Facades\Image;
use Aplus\Pdf\Pdf as AplusPdf;
use Aplus\Pdf\PdfManager;
use Aplus\Pdf\Image as AplusImage;

class SnappyTest extends TestCase
{
    public function testPdfFacadeResolves()
    {
        $pdf = $this->app->make('snappy.pdf');
        $this->assertInstanceOf(PdfManager::class, $pdf);
    }

    public function testImageFacadeResolves()
    {
        $this->assertInstanceOf(AplusImage::class, Image::getFacadeRoot());
        $this->assertInstanceOf(\Knp\Snappy\Image::class, Image::getFacadeRoot());
    }
    
    public function testConfigurationIsLoaded()
    {
        $this->assertTrue(config('snappy.drivers.wkhtmltopdf.enabled'));
        $this->assertTrue(config('snappy.image.enabled'));
    }

    public function testPdfAlias()
    {
        $this->assertInstanceOf(PdfManager::class, app('snappy.pdf'));
    }

    public function testImageAlias()
    {
        $this->assertInstanceOf(AplusImage::class, app('snappy.image'));
    }
}
