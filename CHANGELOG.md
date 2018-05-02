# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

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

## 3.5.3

Released: 2018-04-29

### Fixes

* Fixes `Orchestra\Http\Transformer`.

## 3.5.2

Released: 2017-11-21

### Added

* Add `Orchestra\Http\Transformer`.

## 3.5.1

Released: 2017-10-07

### Changes

* Avoid rebounding `config` on testing environment when it has been bound.

## 3.5.0

Released: 2017-08-25

### Added

* Add `Orchestra\Database\Console\Migrations\FreshCommand`.

### Changes

* Update support for Laravel Framework v5.5.

### Removed

* Remove deprecated `Orchestra\Database\MigrationServiceProvider`.
* Remove deprecated `Orchestra\Database\CacheDecorator::lists()` method.

