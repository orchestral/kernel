# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/kernel`.

## 3.4.0

Released: 2017-05-02

### Changes

* Update support for Laravel Framework v5.4.
* Add `remember` and `rememberForever` as macros to `Illuminate\Database\Eloquent\Builder` via `Orchestra\Database\CachableQueryServiceProvider`.
* Deprecate `Orchestra\Database\MigrationServiceProvider`.

### Removed

* Removed deprecated `Orchestra\Http\Traits\PassThroughTrait`.
* Removed deprecated `Orchestra\Routing\Traits\ControllerResponseTrait`.
