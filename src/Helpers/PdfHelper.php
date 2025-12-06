<?php

declare(strict_types=1);

use Aplus\Pdf\Contracts\DriverInterface;

if (!function_exists('pdf')) {
    /**
     * Get the PDF manager instance.
     *
     * @return \Aplus\Pdf\PdfManager
     */
    function pdf(): \Aplus\Pdf\PdfManager
    {
        return app('aplus.pdf');
    }
}
