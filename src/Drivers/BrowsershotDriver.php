<?php

declare(strict_types=1);

namespace Aplus\Pdf\Drivers;

use Aplus\Pdf\Contracts\DriverInterface;
use Illuminate\Contracts\View\Factory;
use Spatie\Browsershot\Browsershot;

class BrowsershotDriver implements DriverInterface
{
    protected Browsershot $baseBrowsershot;
    protected Factory $viewFactory;
    protected array $defaultConfig;

    public function __construct(Factory $viewFactory, array $config = [], ?Browsershot $browsershot = null)
    {
        $this->viewFactory = $viewFactory;
        $this->defaultConfig = $config;
        $this->baseBrowsershot = $browsershot ?? new Browsershot();
        
        $this->configure($this->baseBrowsershot, $config);
    }

    public function render(string $html, array $options = []): string
    {
        $browsershot = clone $this->baseBrowsershot;
        $browsershot->setHtml($html);
        
        $this->applyOptions($browsershot, $options);
        
        return $browsershot->pdf();
    }

    public function renderFromUrl(string $url, array $options = []): string
    {
        $browsershot = clone $this->baseBrowsershot;
        $browsershot->setUrl($url);
        
        $this->applyOptions($browsershot, $options);
        
        return $browsershot->pdf();
    }

    public function info(): array
    {
        return [
            'name' => 'browsershot',
        ];
    }

    protected function configure(Browsershot $browsershot, array $config)
    {
        if (!empty($config['node_binary'])) {
            $browsershot->setNodeBinary($config['node_binary']);
        }
        if (!empty($config['npm_binary'])) {
            $browsershot->setNpmBinary($config['npm_binary']);
        }
        if (!empty($config['node_modules_path'])) {
            $browsershot->setNodeModulePath($config['node_modules_path']);
        }
    }

    protected function applyOptions(Browsershot $browsershot, array $options)
    {
        // Handle standardized options mapping
        if (isset($options['page-size'])) {
            $browsershot->format($options['page-size']);
            unset($options['page-size']);
        }

        if (isset($options['orientation'])) {
            if (strtolower($options['orientation']) === 'landscape') {
                $browsershot->landscape();
            }
            unset($options['orientation']);
        }

        // Handle margins if set individually
        $margins = [];
        foreach (['top', 'right', 'bottom', 'left'] as $side) {
            $key = "margin-{$side}";
            if (isset($options[$key])) {
                $margins[$side] = $options[$key];
                unset($options[$key]);
            }
        }
        
        if (!empty($margins)) {
            $browsershot->margins(
                $margins['top'] ?? 0,
                $margins['right'] ?? 0,
                $margins['bottom'] ?? 0,
                $margins['left'] ?? 0
            );
        }

        foreach ($options as $key => $value) {
            if (method_exists($browsershot, $key)) {
                if (is_array($value) && !array_is_list($value)) {
                    $browsershot->$key(...$value);
                } elseif (is_array($value)) {
                     $browsershot->$key(...$value);
                } elseif ($value === true) {
                    $browsershot->$key();
                } else {
                    $browsershot->$key($value);
                }
            } else {
                $browsershot->setOption($key, $value);
            }
        }
    }
}
