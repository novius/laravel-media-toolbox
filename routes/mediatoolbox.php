<?php

Route::middleware(config('mediatoolbox.middleware'))
    ->get(
        'storage/'.config('mediatoolbox.prefix').config('mediatoolbox.medias_dirname').'/{query}',
        'Novius\MediaToolbox\Http\Controllers\MediaToolboxController@getPicture'
    )->name('mediatoolbox.query');
