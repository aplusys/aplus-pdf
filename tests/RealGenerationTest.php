<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Builders\PdfBuilder;
use Aplus\Pdf\Facades\Pdf;
use Aplus\Pdf\Managers\BinaryManager;
use Illuminate\Support\Facades\Config;

class RealGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGenerateWkhtmltopdf()
    {
        $manager = new BinaryManager();
        $binary = $manager->detect('wkhtmltopdf');

        if (!$binary) {
            $this->markTestSkipped('wkhtmltopdf binary not found. Run `php artisan snappy:install-binary wkhtmltopdf`');
        }

        Config::set('aplus-pdf.drivers.wkhtmltopdf.binary', $binary);

        $outputFile = __DIR__ . '/../output_wkhtmltopdf.pdf';
        @unlink($outputFile);

        $driver = Pdf::driver('wkhtmltopdf');
        (new PdfBuilder($driver))
            ->html('<h1>Hello Wkhtmltopdf</h1><p>Generated at ' . date('Y-m-d H:i:s') . '</p>')
            ->save($outputFile);

        $this->assertFileExists($outputFile);
        $this->assertGreaterThan(0, filesize($outputFile));
        
        echo "\nGenerated: $outputFile\n";
    }

    public function testGenerateBrowsershot()
    {
        $manager = new BinaryManager();
        
        $outputFile = __DIR__ . '/../output_browsershot.pdf';
        @unlink($outputFile);

        try {
            $driver = Pdf::driver('browsershot');
            (new PdfBuilder($driver))
                ->html('<h1>Hello Browsershot</h1><p>Generated at ' . date('Y-m-d H:i:s') . '</p>')
                ->save($outputFile);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Could not find') || str_contains($e->getMessage(), 'npm')) {
                 $this->markTestSkipped('Browsershot dependencies missing: ' . $e->getMessage());
            }
            throw $e;
        }

        $this->assertFileExists($outputFile);
        $this->assertGreaterThan(0, filesize($outputFile));
        
        echo "\nGenerated: $outputFile\n";
    }
}
