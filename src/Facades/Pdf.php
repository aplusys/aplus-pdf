<?php

namespace Aplus\Pdf\Facades;

use Illuminate\Support\Facades\Facade;
use Aplus\Pdf\Testing\PdfFake;

/**
 * @method static \Aplus\Pdf\Contracts\DriverInterface loadView(string $view, array $data = [], array $mergeData = [])
 * @method static \Aplus\Pdf\Contracts\DriverInterface loadHTML(string $html)
 * @method static \Aplus\Pdf\Contracts\DriverInterface loadFile(string $file)
 * @method static \Aplus\Pdf\Contracts\DriverInterface save(string $filename, bool $overwrite = false)
 * @method static \Illuminate\Http\Response download(string $content = null, string $filename = 'document.pdf')
 * @method static \Illuminate\Http\Response inline(string $content = null, string $filename = 'document.pdf')
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse stream(string $filename = 'document.pdf')
 * @method static \Aplus\Pdf\Contracts\DriverInterface setOption(string $name, mixed $value)
 * @method static \Aplus\Pdf\Contracts\DriverInterface setOptions(array $options)
 * @method static \Aplus\Pdf\Testing\PdfFake fake()
 * 
 * @see \Aplus\Pdf\Pdf
 */
class Pdf extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \Aplus\Pdf\Testing\PdfFake
     */
    public static function fake()
    {
        return static::getFacadeRoot()->fake();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'snappy.pdf'; // Resolves to PdfManager
    }
}
