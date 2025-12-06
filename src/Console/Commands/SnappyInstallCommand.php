<?php

declare(strict_types=1);

namespace Aplus\Pdf\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Storage;

class SnappyInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snappy:install-binary {driver=wkhtmltopdf} {--platform=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install driver binaries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(\Aplus\Pdf\Managers\BinaryManager $manager)
    {
        $driver = $this->argument('driver');
        $platform = $this->option('platform');
        $force = $this->option('force');

        $this->info("Attempting to install $driver...");
        
        if (!$force && $manager->detect($driver)) {
            $this->info("Driver [$driver] is already installed. Use --force to reinstall.");
            return 0;
        }

        if ($manager->install($driver, $platform, $force)) {
            $this->info("Installed successfully.");
            return 0;
        }

        $this->warn("Automatic installation not supported for this driver/platform combination yet.");
        $this->comment("Please check the documentation for manual installation instructions.");
        return 1;
    }
}
