<?php

namespace Orchestra\TestCase;

use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    /**
     * Override aliases.
     *
     * @var array
     */
    protected $aliasesOverride = [];

    /**
     * Override providers.
     *
     * @var array
     */
    protected $providersOverride = [];

    /**
     * Get application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationAliases($app)
    {
        $aliases = parent::getApplicationAliases($app);

        if (empty($this->aliasesOverride)) {
            return $aliases;
        }

        return collect($aliases)
                    ->mapWithKeys(function ($alias, $name) {
                        return array_key_exists($name, $this->aliasesOverride)
                                    ? [$name => $this->aliasesOverride[$name]]
                                    : [$name => $alias];
                    })
                    ->all();
    }

    /**
     * Get application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        $providers = parent::getApplicationProviders($app);

        if (empty($this->providersOverride)) {
            return $providers;
        }

        return collect($providers)
                    ->map(function ($provider) {
                        return array_key_exists($provider, $this->providersOverride)
                                    ? $this->providersOverride[$provider]
                                    : $provider;
                    })
                    ->all();
    }
}
