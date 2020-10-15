<?php

return [
    // Default image fitting ; can be 'cover' or 'stretch'
    'fit' => 'cover',

    // How much time, in seconds, do generated pictures ?
    'expire' => 3600 * 24 * 7,

    'disk' => 'public',

    'medias_dirname' => 'medias',

    // Which middleware use with media route
    'middleware' => [
        'web',
        'cache.headers:public;max_age=604800;etag',
    ],
];
