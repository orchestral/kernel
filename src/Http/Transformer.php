<?php

namespace Orchestra\Http;

use Orchestra\Support\Transformer as BaseTransformer;

class Transformer extends BaseTransformer
{
    use Traits\Transformable;

    /**
     * Resource instance.
     *
     * @var Orchestra\Http\Resources\Json\Resource
     */
    protected $resource;

    /**
     * Invoke the transformation.
     *
     * @param  mixed  $instance
     * @param  array  $options
     *
     * @return mixed
     */
    public static function with($instance, array $options = [])
    {
        return (new static())->options($options)->handle($instance);
    }

    /**
     * Set resource.
     *
     * @param  \Orchestra\Http\Resources\Json|resource $resource
     *
     * @return $this
     */
    public function resource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Invoke the transformer.
     *
     * @param  mixed  $parameters
     *
     * @return mixed
     */
    public function __invoke(...$parameters)
    {
        return $this->transformByMeta(
            'excludes',
            $this->transformByMeta(
                'includes',
                $this->transform(...$parameters),
                ...$parameters
            ),
            ...$parameters
        );
    }
}
