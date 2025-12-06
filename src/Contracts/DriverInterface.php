<?php

declare(strict_types=1);

namespace Aplus\Pdf\Contracts;

interface DriverInterface
{
    /**
     * Render HTML to PDF and return raw bytes.
     *
     * @param string $html
     * @param array $options
     * @return string
     */
    public function render(string $html, array $options = []): string;

    /**
     * Render a URL to PDF and return raw bytes.
     *
     * @param string $url
     * @param array $options
     * @return string
     */
    public function renderFromUrl(string $url, array $options = []): string;

    /**
     * Return metadata about the driver (name, version).
     * 
     * @return array
     */
    public function info(): array;
}
