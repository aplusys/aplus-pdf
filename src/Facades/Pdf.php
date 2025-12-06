<?php

namespace Aplus\Snappy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aplus\Snappy\Pdf setOption(string $name, mixed $value)
 * @method static \Aplus\Snappy\Pdf setOptions(array $options)
 * @method static string getOutput(string|array $input, array $options = [])
 * @method static string getOutputFromHtml(string|array $html, array $options = [])
 * @method static \Illuminate\Http\Response download(string|null $html = null, string $filename = 'document.pdf')
 * @method static \Illuminate\Http\Response inline(string|null $html = null, string $filename = 'document.pdf')
 * @method static \Aplus\Snappy\Pdf setPaper(string $paper, string $orientation = 'portrait')
 * @method static \Aplus\Snappy\Pdf setOrientation(string $orientation)
 * @method static \Aplus\Snappy\Pdf setMargins(float|int $top, float|int $right, float|int $bottom, float|int $left, string $unit = 'mm')
 * @method static \Aplus\Snappy\Pdf loadView(string $view, array $data = [], array $mergeData = [])
 * @method static \Aplus\Snappy\Pdf view(string $view, array $data = [], array $mergeData = [])
 * @method static \Aplus\Snappy\Pdf loadHTML(string|\Illuminate\Contracts\Support\Renderable $html)
 * @method static \Aplus\Snappy\Pdf loadFile(string $file)
 * @method static \Aplus\Snappy\Pdf save(string $filename, bool $overwrite = false)
 *
 * @see \Aplus\Snappy\Pdf
 */
class Pdf extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'snappy.pdf';
    }
}
