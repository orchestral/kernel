Configuration Component for Orchestra Platform
==============

Config Component is a configuration with environment based support for Laravel 5 and above. The component is actually based from Laravel 4 configuration.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/config.svg?style=flat)](https://packagist.org/packages/orchestra/config)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/config.svg?style=flat)](https://packagist.org/packages/orchestra/config)
[![MIT License](https://img.shields.io/packagist/l/orchestra/config.svg?style=flat)](https://packagist.org/packages/orchestra/config)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)

## Version Compatibility

Laravel    | Config
:----------|:----------
 5.0.x     | 3.0.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "orchestra/config": "3.0.*"
    }
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/config=3.0.*"

## Configuration

To swap Laravel 5 default configuration, all you need to do is add the following code to `bootstrap/app.php`:

```php
$app->singleton(
    'Illuminate\Foundation\Bootstrap\LoadConfiguration',
    'Orchestra\Config\Bootstrap\LoadConfiguration'
);
```

### Configuration Caching Support

Config Component also bring `php artisan config:cache` support to speed up configuration loading, in order to do this you need to replace `Illuminate\Foundation\Provider\ArtisanServiceProvider` with a new `App\Providers\ArtisanServiceProvider`:

```php
<?php namespace App\Providers;

use Orchestra\Config\Console\ConfigCacheCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConfigCacheCommand()
    {
        $this->app->singleton('command.config.cache', function ($app) {
            return new ConfigCacheCommand($app['files']);
        });
    }
}
```

Don't forget to update your `config/app.php` to replaces `Illuminate\Foundation\Provider\ArtisanServiceProvider` with `App\Providers\ArtisanServiceProvider`.
