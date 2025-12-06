<?php

declare(strict_types=1);

namespace Aplus\Pdf;

use Illuminate\Support\Manager;
use Aplus\Pdf\Drivers\WkhtmltopdfDriver;
use Aplus\Pdf\Drivers\BrowsershotDriver;
use Aplus\Pdf\Pdf as LegacyPdf;

class PdfManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('snappy.default', 'wkhtmltopdf');
    }

    /**
     * Create an instance of the Wkhtmltopdf driver.
     *
     * @return \Aplus\Pdf\Drivers\WkhtmltopdfDriver
     */
    protected function createWkhtmltopdfDriver()
    {
        // Resolve the legacy Pdf instance which is already configured in registerPdf()
        $legacyPdf = $this->container->make('snappy.pdf.wrapper');
        
        return new WkhtmltopdfDriver($legacyPdf);
    }

    /**
     * Get a new PdfBuilder instance.
     *
     * @return \Aplus\Pdf\Builders\PdfBuilder
     */
    public function builder()
    {
        return new \Aplus\Pdf\Builders\PdfBuilder($this->driver(), $this);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, \Closure $callback)
    {
        return parent::extend($driver, $callback);
    }

    /**
     * Start building a PDF from a view.
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return \Aplus\Pdf\Builders\PdfBuilder
     */
    public function view(string $view, array $data = [], array $mergeData = [])
    {
        return $this->builder()->view($view, $data, $mergeData);
    }

    /**
     * Start building a PDF from HTML.
     *
     * @param string $html
     * @return \Aplus\Pdf\Builders\PdfBuilder
     */
    public function html(string $html)
    {
        return $this->builder()->html($html);
    }

    /**
     * Start building a PDF from a URL.
     *
     * @param string $url
     * @return \Aplus\Pdf\Builders\PdfBuilder
     */
    public function url(string $url)
    {
        return $this->builder()->url($url);
    }

    /**
     * Register the fake driver.
     *
     * @return \Aplus\Pdf\Testing\PdfFake
     */
    public function fake()
    {
        $fake = new \Aplus\Pdf\Testing\PdfFake();
        
        $this->extend('fake', function () use ($fake) {
            return $fake;
        });
        
        $this->config->set('snappy.default', 'fake');
        
        return $fake;
    }

    /**
     * Create an instance of the Browsershot driver.
     *
     * @return \Aplus\Pdf\Drivers\BrowsershotDriver
     */
    protected function createBrowsershotDriver()
    {
        $config = $this->config->get('snappy.drivers.browsershot', []);
        
        return new BrowsershotDriver($this->container['view'], $config);
    }
}
