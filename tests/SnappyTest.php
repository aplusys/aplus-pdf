<?php

namespace Aplus\Snappy\Tests;

use Aplus\Snappy\Facades\Pdf;
use Aplus\Snappy\Facades\Image;
use Aplus\Snappy\Pdf as AplusPdf;
use Aplus\Snappy\Image as AplusImage;

class SnappyTest extends TestCase
{
    public function testPdfFacadeResolves()
    {
        $this->assertInstanceOf(AplusPdf::class, Pdf::getFacadeRoot());
        $this->assertInstanceOf(\Knp\Snappy\Pdf::class, Pdf::getFacadeRoot());
    }

    public function testImageFacadeResolves()
    {
        $this->assertInstanceOf(AplusImage::class, Image::getFacadeRoot());
        $this->assertInstanceOf(\Knp\Snappy\Image::class, Image::getFacadeRoot());
    }
    
    public function testConfigurationIsLoaded()
    {
        $this->assertTrue(config('snappy.pdf.enabled'));
        $this->assertTrue(config('snappy.image.enabled'));
    }

    public function testPdfAlias()
    {
        $this->assertInstanceOf(AplusPdf::class, app('snappy.pdf'));
    }

    public function testImageAlias()
    {
        $this->assertInstanceOf(AplusImage::class, app('snappy.image'));
    }
}
