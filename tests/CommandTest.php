<?php

namespace Aplus\Pdf\Tests;

use Aplus\Pdf\Console\Commands\SnappyInstallCommand;
use Illuminate\Support\Facades\Artisan;

class CommandTest extends TestCase
{
    public function testInstallCommandIsRegistered()
    {
        $input = new \Symfony\Component\Console\Input\ArrayInput(['command' => 'snappy:install-binary']);
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        
        $exitCode = $this->app['Illuminate\Contracts\Console\Kernel']->handle($input, $output);
        
        $outputContent = $output->fetch();
        $this->assertStringContainsString('Attempting to install', $outputContent);
    }
}
