<?php

namespace Novius\MediaToolbox\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredMedias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media-toolbox:purge-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired medias files.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = Storage::disk(config('mediatoolbox.disk', 'public'))
            ->files(config('mediatoolbox.medias_dirname', 'medias'), true);

        $now = now()->timestamp;

        collect($files)->each(function ($file) use ($now) {
            $timestampFile = Storage::disk(config('mediatoolbox.disk', 'public'))
                ->lastModified($file);
            if (($now - $timestampFile) >= (int) config('mediatoolbox.expire')) {
                $this->info(sprintf('Deleting expired file : %s', $file));
                Storage::disk(config('mediatoolbox.disk', 'public'))->delete($file);
            }
        });
    }
}
