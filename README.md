# Novius Media Toolbox for Laravel
[![Travis](https://img.shields.io/travis/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-media-toolbox)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-media-toolbox)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-media-toolbox#licence)

Optimize your pictures on-the-fly! 🛫

## Installation

```sh
composer require novius/laravel-media-toolbox
```

Then add this to `config/app.php`:

```php
// in 'providers' => [ ... ]
\Novius\MediaToolbox\MediaToolboxServiceProvider::class,

// in 'aliases' => [ ... ]
'Medt' => Novius\MediaToolbox\Support\MediaToolbox::class,
```

## Use

In a view:

```html
<!-- This will change the height proportionaly, in order to keep the aspect -->
<img src="{{ Medt::asset('images/hello.png')->width(150) }}">

<!-- It works as well with external pictures. Setting the ratio will force
     previously defined dimensions and change the remaining ones, here the height -->
<img src="{{ Medt::asset('https://example.com/images/logo.png')->width(500)->ratio(16/9) }}">

<!-- fit('cover') is the default behavior. You can just omit it -->
<img src="{{ Medt::asset($user->getPicture())->size(140, 200)->fit('cover') }}">
<img src="{{ Medt::asset($user->getPicture())->size([140, 200])->fit('stretch') }}">

<!-- This will output a jpg with 75% quality.
     Lower number makes smaller files. Minimum is 1, max is 100 -->
<img src="{{ Medt::asset('images/something.gif')->quality(75) }}">
```

## Configure

Publish the config file:

```sh
php artisan vendor:publish --provider="Novius\MediaToolbox\MediaToolboxServiceProvider"
```

Edit `config/mediatoolbox.php`. Here is an example of possible settings:

```php
<?php

return [
    // Fallback in case of file not found or format not supported
    'placeholder' => 'image/placeholder.png',

    // Default image fitting ; can be 'cover' or 'stretch'
    'fit' => 'cover',

    // Where to store pictures. Your stores are defined in config/cache.php
    'cache' => 'file',

    // How much time, in minutes, do generated pictures last?
    'expire' => 60 * 8,
];
```

Don’t worry too much about cache expiration: last modification dates are taken
in account by the engine so you won’t have to clear the cache after editing a
picture.

## Clearing the cache manually

```sh
php artisan cache:clear
```
