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
