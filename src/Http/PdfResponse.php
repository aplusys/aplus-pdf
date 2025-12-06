<?php

namespace Aplus\Pdf\Http;

use Illuminate\Http\Response;

class PdfResponse extends Response
{
    public function __construct(string $content, string $filename, string $disposition = 'inline', array $headers = [])
    {
        $headers = array_merge([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            'Content-Length' => strlen($content),
        ], $headers);

        parent::__construct($content, 200, $headers);
    }
}
