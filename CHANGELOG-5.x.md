# Changelog for 5.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

## 5.1.0

Release: 2020-04-03

### Changes

* Use `$this->container` instead of deprecated `$this->app` for manager extending `Illuminate\Support\Manager`.

### Fixes

* Use `app()` instead of `$this->app` as it would bind to different scope under macros.

### Removed

* Remove authenticating route helpers.

## 5.0.0 

Released: 2020-03-08

### Changes

* Update support for Laravel Framework v7.
