<?php

namespace Aplus\Pdf\Builders;

use Aplus\Pdf\Contracts\DriverInterface;
use Aplus\Pdf\Http\PdfResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;

class PdfBuilder
{
    use Macroable;

    protected $driver;
    protected $manager;
    protected $html = null;
    protected $url = null;
    protected $options = [];

    public function __construct(DriverInterface $driver, ?\Aplus\Pdf\PdfManager $manager = null)
    {
        $this->driver = $driver;
        $this->manager = $manager;
    }

    public function driver(string $driverName): self
    {
        if ($this->manager) {
            $this->driver = $this->manager->driver($driverName);
        }
        return $this;
    }

    public function view(string $view, array $data = [], array $mergeData = []): self
    {
        $this->html = view($view, $data, $mergeData)->render();
        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function option(string $key, mixed $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function options(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function setPaper(string $paper, string $orientation = 'portrait'): self
    {
        $this->options['page-size'] = $paper;
        if ($orientation) {
            $this->options['orientation'] = $orientation;
        }
        return $this;
    }

    public function setOrientation(string $orientation): self
    {
        $this->options['orientation'] = $orientation;
        return $this;
    }

    public function setMargins(float|int $top, float|int $right, float|int $bottom, float|int $left, string $unit = 'mm'): self
    {
        $this->options['margin-top'] = $top . $unit;
        $this->options['margin-right'] = $right . $unit;
        $this->options['margin-bottom'] = $bottom . $unit;
        $this->options['margin-left'] = $left . $unit;
        return $this;
    }

    public function header(string $key, string $value): self
    {
        // Headers handled in options for most drivers or response? 
        // Wkhtmltopdf has specific header options usually.
        // For now, let's treat them as options if relevant, or response headers.
        // Assuming response headers for now if it's about the HTTP response.
        // But if it's PDF headers (like footer-html), that's an option.
        // Let's assume this is for HTTP Response headers for now.
        // Actually, TDD says "headers" but PdfResponse handles HTTP headers.
        // I will add a separate property for HTTP response headers.
        $this->responseHeaders[$key] = $value;
        return $this;
    }

    protected $responseHeaders = [];

    public function save(string $path): bool
    {
        $content = $this->render();
        return file_put_contents($path, $content) !== false;
    }

    public function download(string $filename = 'document.pdf'): PdfResponse
    {
        return new PdfResponse(
            $this->render(),
            $filename,
            'attachment',
            $this->responseHeaders
        );
    }

    public function inline(string $filename = 'document.pdf'): PdfResponse
    {
        return new PdfResponse(
            $this->render(),
            $filename,
            'inline',
            $this->responseHeaders
        );
    }

    public function stream(string $filename = 'document.pdf'): PdfResponse
    {
        // Stream acts like inline usually in browser context
        return $this->inline($filename);
    }

    public function output(): string
    {
        return $this->render();
    }

    protected function render(): string
    {
        if ($this->url) {
            return $this->driver->renderFromUrl($this->url, $this->options);
        }

        return $this->driver->render($this->html ?? '', $this->options);
    }
}
