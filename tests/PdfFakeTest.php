<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Facades\Apdf;

class PdfFakeTest extends TestCase
{
    public function testPdfFakeSwapsInstance()
    {
        Apdf::fake();
        
        // Use the fluent builder
        Apdf::html('<h1>Test</h1>')
            ->save('invoice.pdf');
        
        Apdf::assertRenderedHtml('<h1>Test</h1>');

    }
    
    public function testFluentHelperWithFake()
    {
        Apdf::fake();
        
        pdf()->html('<h1>Fluent</h1>')->save('fluent.pdf');
        
        Apdf::assertRenderedHtml('<h1>Fluent</h1>');
    }
}
