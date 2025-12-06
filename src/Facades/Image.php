<?php

namespace Aplus\Pdf\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aplus\Pdf\Image setOption(string $name, mixed $value)
 * @method static \Aplus\Pdf\Image setOptions(array $options)
 * @method static string getOutput(string|array $input, array $options = [])
 * @method static string getOutputFromHtml(string|array $html, array $options = [])
 * @method static \Illuminate\Http\Response download(string|null $html = null, string $filename = 'image.jpg')
 * @method static \Illuminate\Http\Response inline(string|null $html = null, string $filename = 'image.jpg')
 * @method static \Aplus\Pdf\Image loadView(string $view, array $data = [], array $mergeData = [])
 * @method static \Aplus\Pdf\Image view(string $view, array $data = [], array $mergeData = [])
 * @method static \Aplus\Pdf\Image loadHTML(string|\Illuminate\Contracts\Support\Renderable $html)
 * @method static \Aplus\Pdf\Image loadFile(string $file)
 *
 * @see \Aplus\Pdf\Image
 */
class Image extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'aplus.image';
    }
}
