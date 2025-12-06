<?php

namespace Aplus\Snappy\Tests;

use Aplus\Snappy\Pdf;

class PdfDefaultConfigurationTest extends TestCase
{
    public function testDefaultConfigIsApplied()
    {
        // Must resolve from container to get the configured instance
        $pdf = app('snappy.pdf'); 
        $options = $pdf->getOptions();

        $this->assertEquals(0, $options['margin-top']);
        $this->assertEquals(0, $options['margin-right']);
        $this->assertEquals(0, $options['margin-bottom']);
        $this->assertEquals(0, $options['margin-left']);
        $this->assertEquals('a4', $options['page-size']);
        $this->assertEquals('portrait', $options['orientation']);
    }
}
