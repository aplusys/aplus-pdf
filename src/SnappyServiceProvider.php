<?php

namespace Aplus\Snappy;

use Illuminate\Support\ServiceProvider;
use Knp\Snappy\Image as SnappyImage;
use Knp\Snappy\Pdf as SnappyPdf;

class SnappyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/snappy.php', 'snappy');

        $this->registerPdf();
        $this->registerImage();
    }

    /**
     * Register the Pdf instance.
     *
     * @return void
     */
    protected function registerPdf()
    {
        $this->app->singleton('snappy.pdf', function ($app) {
            $config = $app['config']->get('snappy.pdf');

            $snappy = new Pdf($config['binary']);
            $snappy->setOptions($config['options']);
            
            if ($config['timeout'] !== false) {
                $snappy->setTimeout($config['timeout']);
            }
            
            if (!empty($config['env'])) {
                $snappy->setEnv($config['env']);
            }

            $snappy->setViewFactory($app['view']);

            return $snappy;
        });

        $this->app->alias('snappy.pdf', Pdf::class);
        $this->app->alias('snappy.pdf', SnappyPdf::class);
    }

    /**
     * Register the Image instance.
     *
     * @return void
     */
    protected function registerImage()
    {
        $this->app->singleton('snappy.image', function ($app) {
            $config = $app['config']->get('snappy.image');

            $snappy = new Image($config['binary']);
            $snappy->setOptions($config['options']);
            
            if ($config['timeout'] !== false) {
                $snappy->setTimeout($config['timeout']);
            }

            if (!empty($config['env'])) {
                $snappy->setEnv($config['env']);
            }

            return $snappy;
        });

        $this->app->alias('snappy.image', Image::class);
        $this->app->alias('snappy.image', SnappyImage::class);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/snappy.php' => config_path('snappy.php'),
            ], 'config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['snappy.pdf', 'snappy.image', Pdf::class, SnappyPdf::class, Image::class, SnappyImage::class];
    }
}
