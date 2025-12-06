<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Facades\Pdf;

class PdfFakeTest extends TestCase
{
    public function testPdfFakeSwapsInstance()
    {
        Pdf::fake();
        
        // Use the fluent builder
        Pdf::html('<h1>Test</h1>')
            ->save('invoice.pdf');
        
        Pdf::assertRenderedHtml('<h1>Test</h1>');

    }
    
    public function testFluentHelperWithFake()
    {
        Pdf::fake();
        
        pdf()->html('<h1>Fluent</h1>')->save('fluent.pdf');
        
        Pdf::assertRenderedHtml('<h1>Fluent</h1>');
    }
}
