<?php

namespace Aplus\Pdf\Managers;

use Aplus\Pdf\Contracts\BinaryManagerInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BinaryManager implements BinaryManagerInterface
{
    public function detect(string $driver): ?string
    {
        if ($driver === 'wkhtmltopdf') {
            return $this->detectWkhtmltopdf();
        }
        
        if ($driver === 'chromium') {
            return $this->detectChromium();
        }

        if ($driver === 'playwright') {
             // Check if playwright exists in node_modules
             if (file_exists(base_path('node_modules/playwright'))) {
                 return 'installed';
             }
             return null;
        }

        return null;
    }

    public function install(string $driver, ?string $platform = null, bool $force = false): bool
    {
        // Check if already installed
        if (!$force && $this->detect($driver)) {
             return true;
        }

        if ($driver === 'chromium' || $driver === 'browsershot') {
            return $this->installChromium();
        }
        
        if ($driver === 'wkhtmltopdf') {
            if (PHP_OS_FAMILY === 'Linux') {
                return $this->installWkhtmltopdfLinux();
            }

            if (PHP_OS_FAMILY === 'Darwin') {
                // Return false but users will see the warning from the command
                // or we could throw specific exception to handle message upstream
                // For now, let's try to run brew if possible?
                // Usually safer to just tell them.
                return false;
            }
            
            return false; 
        }

        return false;
    }

    protected function installWkhtmltopdfLinux(): bool
    {
        // Check for jammy (Ubuntu 22.04) or similar. 
        // This is a naive implementation assuming Ubuntu/Debian for now as it's the most common for Laravel servers (Forge etc).
        // Ideally we check /etc/os-release.

        $url = 'https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-2/wkhtmltox_0.12.6.1-2.jammy_amd64.deb';
        $file = 'wkhtmltox.deb';

        // Download
        $process = new Process(['wget', '-O', $file, $url]);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            // Try curl
             $process = new Process(['curl', '-L', '-o', $file, $url]);
             $process->setTimeout(600);
             $process->run();
             if (!$process->isSuccessful()) {
                 return false;
             }
        }

        // Install
        $process = Process::fromShellCommandline("sudo apt-get install -y ./$file");
        $process->setTimeout(600);
        $process->run();
        
        // Cleanup
        @unlink($file);

        return $process->isSuccessful();
    }

    protected function installChromium(): bool
    {
        // Check for npm
        $process = new Process(['npm', '-v']);
        $process->run();
        
        if (!$process->isSuccessful()) {
            return false;
        }

        // 1. Install puppeteer
        $process = new Process(['npm', 'install', 'puppeteer']);
        $process->setTimeout(300);
        $process->run();
        
        if (!$process->isSuccessful()) {
            return false;
        }

        // 2. Install Chrome binary
        $process = new Process(['npx', 'puppeteer', 'browsers', 'install', 'chrome']);
        $process->setTimeout(300);
        $process->run();

        // 3. Install System Dependencies (Linux only)
        if (PHP_OS_FAMILY === 'Linux') {
            $this->installLinuxDependencies();
        }

        if ($driver === 'playwright') {
            return $this->installPlaywright();
        }
        
        return true;
    }

    protected function installPlaywright(): bool
    {
        // 1. Install playwright lib
        $process = new Process(['npm', 'install', 'playwright']);
        $process->setTimeout(600);
        $process->run();
        
        if (!$process->isSuccessful()) {
            return false;
        }

        // 2. Install Browsers
        // Installing all supported browsers ensures we get chromium and its headless-shell if split
        $process = new Process(['npx', 'playwright', 'install'], null, [
             'PLAYWRIGHT_BROWSERS_PATH' => base_path('storage/playwright') // Defaulting to project storage
        ]);
        $process->setTimeout(1200);
        $process->run();
        
        // 3. Install System Deps
        $process = new Process(['npx', 'playwright', 'install-deps'], null, [
             'PLAYWRIGHT_BROWSERS_PATH' => base_path('storage/playwright')
        ]);
        $process->setTimeout(1200);
        $process->run();

        return $process->isSuccessful();
    }

    protected function installLinuxDependencies()
    {
        // List from Spatie Browsershot docs
        $libs = [
            'libx11-xcb1', 'libxcomposite1', 'libasound2', 'libatk1.0-0', 
            'libatk-bridge2.0-0', 'libcairo2', 'libcups2', 'libdbus-1-3', 
            'libexpat1', 'libfontconfig1', 'libgbm1', 'libgcc1', 'libglib2.0-0', 
            'libgtk-3-0', 'libnspr4', 'libpango-1.0-0', 'libpangocairo-1.0-0', 
            'libstdc++6', 'libx11-6', 'libxcb1', 'libxcursor1', 
            'libxdamage1', 'libxext6', 'libxfixes3', 'libxi6', 'libxrandr2', 
            'libxrender1', 'libxss1', 'libxtst6'
        ];

        // Try to install using apt-get if we have sudo or root
        // Note: libasound2t64 is for Ubuntu 24.04+. Older systems might need libasound2.
        // We will try running the command, but capture output to warn username if sudo needed.
        
        $cmd = 'sudo apt-get update && sudo apt-get install -y ' . implode(' ', $libs);
        
        $process = new Process(explode(' ', $cmd)); // This won't work well with && and shell builtins needing shell wrapper
        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            // We can't easily print from here as this is a Manager class, 
            // but we can log or essentially fail silently on deps but return true for the main binary.
            // In a real CLI command we'd output this info.
            // Let's rely on the VerifyCommand to check specific shared libs or just let the user know via returning false?
            // For now, we will return false for "full success" only if deps install, 
            // but that might block people who already have deps.
            
            // Best approach: try to install, if fail, we assume it's permissions or distro mismatch,
            // but we proceed because puppeteer is installed.
            return; 
        }
    }

    public function verify(string $binaryPath): bool
    {
        try {
            $process = new Process([$binaryPath, '--version']);
            $process->run();
            return $process->isSuccessful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function detectWkhtmltopdf(): ?string
    {
        // check config first? passed in wrapper?
        // simple shell check
        $paths = [
            '/usr/local/bin/wkhtmltopdf',
            '/usr/bin/wkhtmltopdf',
            '/opt/homebrew/bin/wkhtmltopdf',
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        // Try `which`
        $process = new Process(['which', 'wkhtmltopdf']);
        $process->run();
        if ($process->isSuccessful()) {
            return trim($process->getOutput());
        }

        return null;
    }

    protected function detectChromium(): ?string
    {
        // similar check for chromium-browser or google-chrome
        $names = ['google-chrome', 'chromium', 'chromium-browser'];
        
        foreach ($names as $name) {
             $process = new Process(['which', $name]);
             $process->run();
             if ($process->isSuccessful()) {
                 return trim($process->getOutput());
             }
        }

        return null;
    }
}
