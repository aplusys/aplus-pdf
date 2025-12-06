<?php

declare(strict_types=1);

namespace Aplus\Pdf\Testing;

use Aplus\Pdf\Contracts\DriverInterface;
use PHPUnit\Framework\Assert as PHPUnit;

class PdfFake implements DriverInterface
{
    protected $renderedHtml = [];
    protected $renderedUrls = [];

    public function render(string $html, array $options = []): string
    {
        $this->renderedHtml[] = ['html' => $html, 'options' => $options];
        return 'FAKE_PDF_BINARY_CONTENT';
    }

    public function renderFromUrl(string $url, array $options = []): string
    {
        $this->renderedUrls[] = ['url' => $url, 'options' => $options];
        return 'FAKE_PDF_BINARY_FROM_URL';
    }

    public function info(): array
    {
        return ['name' => 'fake'];
    }

    // -- Assertions --

    public function assertRenderedHtml(string $htmlPart)
    {
        $found = false;
        foreach ($this->renderedHtml as $item) {
            if (str_contains($item['html'], $htmlPart)) {
                $found = true;
                break;
            }
        }
        
        PHPUnit::assertTrue($found, "Failed asserting that HTML containing [{$htmlPart}] was rendered.");
    }

    public function assertRenderedUrl(string $url)
    {
        $found = false;
        foreach ($this->renderedUrls as $item) {
            if ($item['url'] === $url) {
                $found = true;
                break;
            }
        }
         PHPUnit::assertTrue($found, "Failed asserting that URL [{$url}] was rendered.");
    }
}
