<?php

namespace Aplus\Snappy\Tests;

use Aplus\Snappy\Pdf;
use Mockery;

class IoTest extends TestCase
{
    public function testLoadFileAndDownload()
    {
        // Mock the class, specifically 'getOutput'
        $pdf = Mockery::mock(Pdf::class . '[getOutput]', ['/usr/bin/wkhtmltopdf']);
        
        $pdf->shouldReceive('getOutput')
            ->once()
            ->with('foo.html')
            ->andReturn('PDF DATA');

        $pdf->loadFile('foo.html');
        $response = $pdf->download();
        
        $this->assertEquals('PDF DATA', $response->getContent());
    }

    public function testSaveFromHtml()
    {
        // Mock "generateFromHtml" which is called when html content is present
        // Note: Pdf extends Snappy\Pdf, generateFromHtml is on Snappy\Pdf.
        // We need to mock it on our Pdf class.
        $pdf = Mockery::mock(Pdf::class . '[generateFromHtml]', ['/usr/bin/wkhtmltopdf']);

        $pdf->shouldReceive('generateFromHtml')
            ->once()
            ->with('<h1>HTML</h1>', 'output.pdf', [], false);

        $pdf->loadHTML('<h1>HTML</h1>')
            ->save('output.pdf');
            
        $this->assertTrue(true); // Asserting no exception/mock expectations
    }

    public function testSaveFromFile()
    {
        // Mock "generate" which is called when file is present
        $pdf = Mockery::mock(Pdf::class . '[generate]', ['/usr/bin/wkhtmltopdf']);

        $pdf->shouldReceive('generate')
            ->once()
            ->with('input.html', 'output.pdf', [], false);

        $pdf->loadFile('input.html')
            ->save('output.pdf');
            
        $this->assertTrue(true);
    }

    public function testSetOptionFluent()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        // 'page-size' is a valid option
        $result = $pdf->setOption('page-size', 'A4');
        
        $this->assertSame($pdf, $result);
        $this->assertEquals('A4', $pdf->getOptions()['page-size']);
    }

    public function testSetOptionsFluent()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        $result = $pdf->setOptions(['orientation' => 'landscape']);
        
        $this->assertSame($pdf, $result);
        $this->assertEquals('landscape', $pdf->getOptions()['orientation']);
    }
}
