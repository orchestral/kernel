# Changelog for 4.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

## 4.2.0

Released: 2020-01-04

### Added 

* Added `Orchestra\Database\SearchServiceProvider`.

## 4.1.0

Released: 2019-12-29

### Changes

* Implements console exit code.
* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function.

## 4.0.1

Released: 2019-10-30

### Fixes

* Fixes usages of `orchestra.postal`.

## 4.0.0 

Released: 2019-09-04

### Added

* Added `Orchestra\Reauthenticate` based on [mpociot/reauthenticate](https://github.com/mpociot/reauthenticate).

### Changes

* Update support for Laravel Framework v6.0.
