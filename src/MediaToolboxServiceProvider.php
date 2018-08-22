<?php

namespace Novius\MediaToolbox;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class MediaToolboxServiceProvider extends LaravelServiceProvider
{
    const PACKAGE_NAME = 'laravel-mediatoolbox';
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/config' => config_path()], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/mediatoolbox.php',
            'mediatoolbox'
        );

        $this->loadRoutesFrom(
            __DIR__.'/routes/mediatoolbox.php'
        );
    }
}
