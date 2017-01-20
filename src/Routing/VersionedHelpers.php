<?php

namespace Orchestra\Routing;

use Orchestra\Support\Transformer;
use Illuminate\Contracts\Support\Arrayable;

trait VersionedHelpers
{
    /**
     * Transform and serialize the instance.
     *
     * @param  \Orchestra\Model\Eloquent|\Illuminate\Support\Collection  $instance
     * @param  string  $name
     * @param  string|null  $serializer
     * @param  array  $options
     *
     * @return array
     */
    protected function transform($instance, $transformer, $serializer = null, array $options = [])
    {
        if (is_null($serializer)) {
            $serializer = $transformer;
        }

        return $this->serializeWith(
            $this->transformWith($instance, $transformer, $options),
            $serializer,
            $options
        );
    }

    /**
     * Process the instance.
     *
     * @param  \Orchestra\Model\Eloquent|\Illuminate\Support\Collection  $instance
     * @param  string  $name
     * @param  string  $method
     * @param  mixed  $parameters
     *
     * @return mixed
     */
    protected function processWith($instance, $name, $method, ...$parameters)
    {
        $processor = $this->getVersionedResourceClassName('Processors', $name);

        return app($processor)->{$method}($this, $instance, ...$parameters);
    }

    /**
     * Transform the instance.
     *
     * @param  \Orchestra\Model\Eloquent|\Illuminate\Support\Collection  $instance
     * @param  string  $name
     * @param  array  $options
     *
     * @return mixed
     */
    protected function transformWith($instance, $name, array $options = [])
    {
        $transformer = $this->getVersionedResourceClassName('Transformers', $name);

        if (class_exists($transformer)) {
            $transformer = app($transformer);

            if ($transformer instanceof Transformer) {
                return $transformer->withOptions($options)
                            ->handle($instance);
            }

            return $instance->transform($transformer);
        }

        return $instance;
    }

    /**
     * Serialize the instance.
     *
     * @param  mixed  $instance
     * @param  string  $name
     *
     * @return array
     */
    protected function serializeWith($instance, $name)
    {
        $serializer = $this->getVersionedResourceClassName('Serializers', $name);

        if (class_exists($serializer)) {
            return call_user_func(app($serializer), $instance);
        }

        return $instance instanceof Arrayable ? $instance->toArray() : $instance;
    }

    /**
     * Get versioned resource class name.
     *
     * @param  string  $group
     * @param  string  $name
     *
     * @return string
     */
    abstract function getVersionedResourceClassName($group, $name);
}
