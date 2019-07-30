# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

## 3.8.1

Released: 2019-07-30

### Added

* Added `Orchestra\Http\Transformer\InteractsWithDateTime` trait.

### Changes

* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function.

## 3.8.0 

Released: 2019-03-24

### Changes

* Update support for Laravel Framework v5.8.
* Change configuration file on `Orchestra\Config\Console\ConfigCacheCommand` using composer.json `extra.config-cache` instead of `compile.php` configuration file.

### Remove

* Remove deprecated `Orchestra\Http\Traits\PassThrough`.
* Remove deprecated `Orchestra\Routing\Traits\ControllerResponse`.

## 3.7.0 

Released: 2018-12-25

### Added

* Added `orchestra/hashing`.

### Changes

* Update support for Laravel Framework v5.7.

## 3.6.2

Released: 2018-12-25

### Added

* Add `orchestra/hashing`.

## 3.6.1

Released: 2018-05-02

### Changes

* return `self` should only be used when method is marked as `final`.

### Fixes

* Fixes `Orchestra\Http\Transformer`.

## 3.6.0 

Released: 2018-02-27

### Added

* Added `orchestra/publisher`.

### Changes

* Update support for Laravel Framework v5.6.
* Use PHP 7.1 scalar typehint and return type whenever possible.

### Removed

* Remove `--realpath` support from `orchestra/database` as this is not supported in Laravel Framework.

