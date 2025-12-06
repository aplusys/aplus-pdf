<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Pdf;

class PdfCustomizationTest extends TestCase
{
    public function testSetPaperAndOrientation()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        $pdf->setPaper('a4', 'landscape');

        $options = $pdf->getOptions();
        
        $this->assertArrayHasKey('page-size', $options);
        $this->assertEquals('a4', $options['page-size']);
        $this->assertArrayHasKey('orientation', $options);
        $this->assertEquals('landscape', $options['orientation']);
    }

    public function testSetOrientation()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        $pdf->setOrientation('portrait');

        $options = $pdf->getOptions();

        $this->assertArrayHasKey('orientation', $options);
        $this->assertEquals('portrait', $options['orientation']);
    }

    public function testSetMargins()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        $pdf->setMargins(10, 5, 10, 5);

        $options = $pdf->getOptions();

        $this->assertArrayHasKey('margin-top', $options);
        $this->assertEquals('10mm', $options['margin-top']);
        $this->assertArrayHasKey('margin-right', $options);
        $this->assertEquals('5mm', $options['margin-right']);
        $this->assertArrayHasKey('margin-bottom', $options);
        $this->assertEquals('10mm', $options['margin-bottom']);
        $this->assertArrayHasKey('margin-left', $options);
        $this->assertEquals('5mm', $options['margin-left']);
    }

    public function testSetMarginsWithUnit()
    {
        $pdf = new Pdf('/usr/bin/wkhtmltopdf');
        $pdf->setMargins(1, 0.5, 1, 0.5, 'in');

        $options = $pdf->getOptions();

        $this->assertEquals('1in', $options['margin-top']);
        $this->assertEquals('0.5in', $options['margin-right']);
        $this->assertEquals('1in', $options['margin-bottom']);
        $this->assertEquals('0.5in', $options['margin-left']);
    }
}
