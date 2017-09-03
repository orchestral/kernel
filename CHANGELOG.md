# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

## 3.5.0

Released: 2017-08-25

### Added

* Add `Orchestra\Database\Console\Migrations\FreshCommand`.

### Changes

* Update support for Laravel Framework v5.5.

### Removed

* Remove deprecated `Orchestra\Database\MigrationServiceProvider`.
* Remove deprecated `Orchestra\Database\CacheDecorator::lists()` method.

## 3.4.3

Released: 2017-08-25

### Changes

* Improves migrations artisan commands to match Laravel equivalent.

## 3.4.2

Released: 2017-07-11

### Changes

* Consistently return a Collection when using `Orchestra\Database\CacheDecorator`.

## 3.4.1

Released: 2017-05-14

### Changes

* Add `--path` as array for migrations artisan command.

### Fixes

* Fixes fetching token from session store.

## 3.4.0

Released: 2017-05-02

### Changes

* Update support for Laravel Framework v5.4.
* Add `remember` and `rememberForever` as macros to `Illuminate\Database\Eloquent\Builder` via `Orchestra\Database\CachableQueryServiceProvider`.
* Deprecate `Orchestra\Database\MigrationServiceProvider`.

### Removed

* Removed deprecated `Orchestra\Http\Traits\PassThroughTrait`.
* Removed deprecated `Orchestra\Routing\Traits\ControllerResponseTrait`.
