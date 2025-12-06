<?php

namespace Aplus\Pdf\Jobs;

use Aplus\Pdf\Facades\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RenderPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $view;
    protected $data;
    protected $path;
    protected $disk;
    protected $options;

    public function __construct(string $view, array $data, string $path, string $disk = 'local', array $options = [])
    {
        $this->view = $view;
        $this->data = $data;
        $this->path = $path;
        $this->disk = $disk;
        $this->options = $options;
    }

    public function handle()
    {
        // Use the facade or manager to render
        $content = Pdf::view($this->view, $this->data)
            ->options($this->options)
            ->output();

        Storage::disk($this->disk)->put($this->path, $content);
    }
}
