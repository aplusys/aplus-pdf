<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Pdf;

class PdfDefaultConfigurationTest extends TestCase
{
    public function testDefaultConfigIsApplied()
    {
        // Resolve the underlying legacy wrapper which holds the options
        $pdf = app('snappy.pdf.wrapper'); 
        $options = $pdf->getOptions();

        $this->assertEquals(0, $options['margin-top']);
        $this->assertEquals(0, $options['margin-right']);
        $this->assertEquals(0, $options['margin-bottom']);
        $this->assertEquals(0, $options['margin-left']);
        $this->assertEquals('a4', $options['page-size']);
        $this->assertEquals('portrait', $options['orientation']);
    }
}
