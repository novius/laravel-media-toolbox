<?php

namespace Novius\MediaToolbox\Test;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Novius\MediaToolbox\MediaToolboxServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $assets = [
        __DIR__.'/assets/blank.gif',
        __DIR__.'/assets/blank.jpg',
        __DIR__.'/assets/blank.png',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');

        include_once __DIR__.'/../database/migrations/create_media_toolbox_medias_history.php.stub';

        (new \CreateMediaToolboxMediasHistory())->up();

        $this->setUpAssets();
    }

    public function getEnvironmentSetUp($app)
    {
        $aliases = config('app.aliases');
        $aliases['Medt'] = \Novius\MediaToolbox\Support\MediaToolbox::class;

        $app['config']->set('app.aliases', $aliases);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            MediaToolboxServiceProvider::class,
        ];
    }

    protected function setUpAssets()
    {
        foreach ($this->assets as $path) {
            $filename = pathinfo($path, PATHINFO_BASENAME);
            if (Storage::disk(config('mediatoolbox.disk'))->exists($filename)) {
                continue;
            }

            Storage::disk(config('mediatoolbox.disk'))
                ->put($filename, file_get_contents($path));
        }
    }
}
