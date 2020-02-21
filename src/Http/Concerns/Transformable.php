<?php

namespace Orchestra\Http\Concerns;

use Illuminate\Support\Str;
use Orchestra\Support\Arr;

trait Transformable
{
    /**
     * Transformers' options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Meta types.
     *
     * @var array
     */
    protected $meta = ['includes', 'excludes'];

    /**
     * The request implementation.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Add options.
     *
     * @param  array  $options
     *
     * @return $this
     */
    public function options(array $options = [])
    {
        $this->options = $options;

        foreach ($this->meta as $name) {
            $this->filterMetaType($name);
        }

        return $this;
    }

    /**
     * Get request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        if (\is_null($this->request)) {
            $this->setRequest(\app()->refresh('request', $this, 'setRequest'));
        }

        return $this->request;
    }

    /**
     * Set request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Merge meta options.
     *
     * @param string|array $meta
     * @param array        $options
     *
     * @return array
     */
    protected function merge($meta, array $options = []): array
    {
        if (\is_array($meta) && empty($options)) {
            $options = $meta;
            $meta = null;
        }

        $options = \array_merge(['includes' => [], 'excludes' => []], $options);

        foreach ($options as $key => $value) {
            $filtered = Arr::expand(\array_flip($value));
            $parent = Arr::get($this->options, \is_null($meta) ? $key : "{$key}.{$meta}", []);

            $options[$key] = \array_keys(Arr::dot(\array_merge_recursive($filtered, $parent)));
        }

        return $options;
    }

    /**
     * Resolve includes for transformer.
     *
     * @param  string  $group
     * @param  array  $data
     * @param  mixed  $parameters
     *
     * @return array
     */
    protected function transformByMeta(string $meta, array $data, ...$parameters): array
    {
        $name = Str::singular($meta);
        $types = $this->options[$meta];

        if (empty($types)) {
            return $data;
        }

        foreach ($types as $type => $index) {
            if (\is_array($type)) {
                continue;
            }

            $method = $name.Str::studly($type);

            if (\method_exists($this, $method)) {
                $data = $this->{$method}($data, ...$parameters);
            }
        }

        return $data;
    }

    /**
     * Get option by group.
     *
     * @param  string  $name
     *
     * @return void
     */
    protected function filterMetaType(string $name): void
    {
        $types = $this->options[$name] ?? $this->getRequest()->input($name);

        if (\is_string($types)) {
            $types = \explode(',', $types);
        }

        $this->options[$name] = \is_array($types) ? Arr::expand(\array_flip($types)) : [];
    }
}
