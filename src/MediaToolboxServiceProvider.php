<?php

namespace Novius\MediaToolbox;

use Illuminate\Support\ServiceProvider;
use Novius\MediaToolbox\Console\Commands\PurgeExpiredMedias;

class MediaToolboxServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/config' => config_path()], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PurgeExpiredMedias::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/mediatoolbox.php',
            'mediatoolbox'
        );

        $this->loadRoutesFrom(
            __DIR__.'/../routes/mediatoolbox.php'
        );
    }
}
