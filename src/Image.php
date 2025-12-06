<?php

namespace Aplus\Snappy;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Knp\Snappy\Image as SnappyImage;

class Image extends SnappyImage
{
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $viewFactory;

    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $file;

    /**
     * Set the view factory.
     *
     * @param \Illuminate\Contracts\View\Factory $viewFactory
     * @return $this
     */
    public function setViewFactory($viewFactory)
    {
        $this->viewFactory = $viewFactory;
        return $this;
    }

    /**
     * Set a snappy option.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        parent::setOption($name, $value);
        return $this;
    }

    /**
     * Set snappy options.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);
        return $this;
    }

    /**
     * Load a view.
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return $this
     */
    public function loadView($view, $data = [], $mergeData = [])
    {
        $this->html = $this->viewFactory->make($view, $data, $mergeData)->render();
        $this->file = null;
        return $this;
    }

    /**
     * Load HTML string.
     * 
     * @param string|\Illuminate\Contracts\Support\Renderable $html
     * @return $this
     */
    public function loadHTML($html)
    {
        if ($html instanceof \Illuminate\Contracts\Support\Renderable) {
            $html = $html->render();
        }

        $this->html = $html;
        $this->file = null;
        return $this;
    }

    /**
     * Load content from a file.
     *
     * @param string $file
     * @return $this
     */
    public function loadFile($file)
    {
        $this->file = $file;
        $this->html = null;
        return $this;
    }

    /**
     * Alias for loadView.
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return $this
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return $this->loadView($view, $data, $mergeData);
    }

    /**
     * Save the Image to a file.
     *
     * @param string $filename
     * @param bool $overwrite
     * @return $this
     */
    public function save($filename, $overwrite = false)
    {
        if ($this->html) {
            $this->generateFromHtml($this->html, $filename, [], $overwrite);
        } elseif ($this->file) {
            $this->generate($this->file, $filename, [], $overwrite);
        }

        return $this;
    }

    /**
     * Get the Image as a response with the correct Content-Type.
     *
     * @param string|null $content
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function download($content = null, $filename = 'image.jpg')
    {
        if ($content) {
             $output = $this->getOutputFromHtml($content);
        } elseif ($this->html) {
            $output = $this->getOutputFromHtml($this->html);
        } elseif ($this->file) {
            $output = $this->getOutput($this->file);
        } else {
            $output = '';
        }

        return ResponseFacade::make($output, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get the Image as an inline response.
     *
     * @param string|null $content
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function inline($content = null, $filename = 'image.jpg')
    {
        if ($content) {
             $output = $this->getOutputFromHtml($content);
        } elseif ($this->html) {
            $output = $this->getOutputFromHtml($this->html);
        } elseif ($this->file) {
            $output = $this->getOutput($this->file);
        } else {
            $output = '';
        }

        return ResponseFacade::make($output, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
