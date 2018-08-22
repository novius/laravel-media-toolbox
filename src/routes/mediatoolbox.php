<?php

Route::middleware(['web'])
->get(
    'mediatoolbox/getpicture',
    'Novius\MediaToolbox\Http\Controllers\MediaToolboxController@getPicture'
)->name('mediatoolbox.query');
