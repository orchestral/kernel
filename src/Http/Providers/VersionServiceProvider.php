<?php

namespace Orchestra\Http\Providers;

use Illuminate\Support\ServiceProvider;
use Orchestra\Http\VersionControl;

class VersionServiceProvider extends ServiceProvider
{
    /**
     * List of supported version.
     *
     * @var array
     */
    protected $versions = [];

    /**
     * Default version.
     *
     * @var string
     */
    protected $defaultVersion;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.http.version', function (Application $app) {
            return \tap(new VersionControl(), function ($version) {
                $this->registerSupportedVersions($version);
            });
        });
    }

    /**
     * Register supported version.
     */
    protected function registerSupportedVersions(VersionControl $version): void
    {
        foreach ($this->versions as $code => $namespace) {
            $version->addVersion($code, $namespace);
        }

        if (! \is_null($this->defaultVersion)) {
            $version->setDefaultVersion($this->defaultVersion);
        }
    }
}
