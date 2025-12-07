<?php

declare(strict_types=1);

namespace Aplus\Pdf\Drivers;

use Aplus\Pdf\Contracts\DriverInterface;
use Symfony\Component\Process\Process;
use RuntimeException;

class PlaywrightDriver implements DriverInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function render(string $html, array $options = []): string
    {
        $inputPath = tempnam(sys_get_temp_dir(), 'playwright_input_') . '.html';
        $outputPath = tempnam(sys_get_temp_dir(), 'playwright_output_') . '.pdf';

        file_put_contents($inputPath, $html);

        try {
            $this->runScript($inputPath, $outputPath, $options);
            
            if (!file_exists($outputPath)) {
                throw new RuntimeException("Playwright did not generate output file.");
            }

            return file_get_contents($outputPath);
        } finally {
            if (file_exists($inputPath)) {
                unlink($inputPath);
            }
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
        }
    }

    public function renderFromUrl(string $url, array $options = []): string
    {
        // For URL rendering, we could adapt the script to take a URL instead of file content.
        // However, standard interface usually assumes HTML content is passed in render.
        // But renderFromUrl is also in interface.
        // Our script currently reads file content. 
        // We can just fetch the URL content here or update script to handle URL.
        // Updating script is better for correct asset loading (relative paths).
        
        // LIMITATION: Current script assumes input is HTML file content to be set via setContent.
        // If we want to support renderFromUrl properly (navigating to URL), we should update script or logic.
        // For now, let's fetch content to keep simple, OR create a wrapper HTML that redirects? No.
        
        // Let's download content? No, that breaks relative links.
        // Let's assume for now we implement render (HTML string) primarily.
        // To implement renderFromUrl, I would need to modify the script to accept a URL mode.
        // For this first version, I'll fetch and render.
        
        $html = file_get_contents($url);
        if ($html === false) {
             throw new RuntimeException("Could not read URL: $url");
        }
        
        return $this->render($html, $options);
    }

    public function info(): array
    {
        return [
            'name' => 'playwright',
        ];
    }

    protected function runScript(string $inputPath, string $outputPath, array $options): void
    {
        $nodeBinary = $this->config['node_binary'] ?? 'node';
        $scriptPath = $this->config['script_path'] ?? __DIR__ . '/../Scripts/playwright.cjs';
        
        // Prepare options
        $scriptOptions = [
            'format' => $options['page-size'] ?? 'A4',
            'landscape' => isset($options['orientation']) && strtolower($options['orientation']) === 'landscape',
            'executablePath' => $this->config['chromium_binary'] ?? null,
            'waitUntil' => 'networkidle', // Default to networkidle
        ];

        // Handle margins
        $margins = [];
        foreach (['top', 'right', 'bottom', 'left'] as $side) {
            $key = "margin-{$side}";
            if (isset($options[$key])) {
                $margins[$side] = $options[$key];
            }
        }
        if (!empty($margins)) {
            $scriptOptions['margin'] = $margins;
        }
        
        // Pass other options directly if needed
        $scriptOptions['file_options'] = array_diff_key($options, [
            'page-size' => 1, 'orientation' => 1, 
            'margin-top' => 1, 'margin-right' => 1, 'margin-bottom' => 1, 'margin-left' => 1
        ]);

        $command = [
            $nodeBinary,
            $scriptPath,
            $inputPath,
            $outputPath,
            json_encode($scriptOptions),
        ];

        $process = new Process($command);
        $process->setTimeout($this->config['timeout'] ?? 60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException("Playwright PDF generation failed: " . $process->getErrorOutput());
        }
    }
}
