<?php

$prefix = \Illuminate\Support\Facades\Storage::disk(config('mediatoolbox.disk', 'public'))
    ->getDriver()->getAdapter()->getPathPrefix();
$prefix = ltrim(str_replace(storage_path('app/public'), '', $prefix), '/');

Route::middleware(config('mediatoolbox.middleware'))
    ->get(
        'storage/'.$prefix.config('mediatoolbox.medias_dirname').'/{query}',
        'Novius\MediaToolbox\Http\Controllers\MediaToolboxController@getPicture'
    )->name('mediatoolbox.query');
