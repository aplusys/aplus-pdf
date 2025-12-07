<?php

namespace Aplus\Pdf\Console\Commands;

use Aplus\Pdf\Managers\BinaryManager;
use Illuminate\Console\Command;

class VerifyCommand extends Command
{
    protected $signature = 'pdf:verify {binary}';

    protected $description = 'Verify the given binary path is executable';

    public function handle(BinaryManager $manager)
    {
        $binary = $this->argument('binary');

        $this->info("Verifying [$binary]...");

        if ($manager->verify($binary)) {
            $this->info("Binary is executable and working.");
            return 0;
        }

        $this->error("Binary verification failed.");
        return 1;
    }
}
