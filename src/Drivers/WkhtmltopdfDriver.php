<?php

declare(strict_types=1);

namespace Aplus\Pdf\Drivers;

use Aplus\Pdf\Contracts\DriverInterface;
use Aplus\Pdf\Pdf as LegacyPdf;

class WkhtmltopdfDriver implements DriverInterface
{
    protected LegacyPdf $pdf;
    
    public function __construct(LegacyPdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function render(string $html, array $options = []): string
    {
        $this->pdf->setOptions($options);
        return $this->pdf->getOutputFromHtml($html);
    }

    public function renderFromUrl(string $url, array $options = []): string
    {
        $pdf = clone $this->pdf;
        
        $pdf->loadFile($url);
        $pdf->setOptions($options);
        
        return $pdf->getOutput($url);
    }

    public function info(): array
    {
        return [
            'name' => 'wkhtmltopdf',
            'version' => 'unknown', // Could run binary --version
        ];
    }
}
