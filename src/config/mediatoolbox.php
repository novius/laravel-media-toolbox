<?php

return [
    // Fallback in case of file not found or format not supported
    'placeholder' => '/images/placeholder.png',

    // Default image fitting ; can be 'cover' or 'stretch'
    'fit' => 'cover',

    // Where to store pictures. Your stores are defined in config/cache.php
    'cache' => 'file',

    // How much time, in minutes, do generated pictures last?
    'expire' => 60 * 8,
];
