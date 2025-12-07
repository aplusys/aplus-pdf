<?php

namespace Aplus\Pdf\Console\Commands;

use Aplus\Pdf\Managers\BinaryManager;
use Illuminate\Console\Command;

class DetectBinaryCommand extends Command
{
    protected $signature = 'pdf:detect-binary {driver? : The driver name (wkhtmltopdf, chromium)}';

    protected $description = 'Detect the path to the binary for the given driver';

    public function handle(BinaryManager $manager)
    {
        $driver = $this->argument('driver') ?? 'wkhtmltopdf';
        
        $this->info("Detecting binary for [$driver]...");

        $path = $manager->detect($driver);

        if ($path) {
            $this->info("Found: $path");
            return 0;
        }

        $this->error("Could not auto-detect binary for [$driver].");
        return 1;
    }
}
