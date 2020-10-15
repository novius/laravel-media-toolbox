# Novius Media Toolbox for Laravel
[![Travis](https://img.shields.io/travis/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-media-toolbox)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-media-toolbox)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-media-toolbox.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-media-toolbox#licence)

Optimize your pictures on-the-fly! 🛫

## Requirements

Laravel >= 6.0

## Installation

```sh
composer require novius/laravel-media-toolbox
```

Then add this to `config/app.php`:

```php
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

**SEO Friendly**

You can specify a name for your filename (value will be converted to slug) :

Usage in a view:

```html
<img src="{{ Medt::asset('images/hello.png')->quality(90)->name('my image') }}">
```

## Configure

Publish the config file:

```sh
php artisan vendor:publish --provider="Novius\MediaToolbox\MediaToolboxServiceProvider"
```

Edit `config/mediatoolbox.php`.

## Clearing the cache manually

```sh
php artisan cache:clear
```

## Purge expired medias

In your app/Console/Kernel.php file, you should register a daily job to purge expired medias :

protected function schedule(Schedule $schedule)
{
    $schedule->command('media-toolbox:purge-expired')
        ->daily();
}

By default, media is stale considered after 1 week. You can override this value in configuration file with `expire key.

## Lint

Run php-cs with:

```sh
composer run-script lint
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.

