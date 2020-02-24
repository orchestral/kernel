<?php

namespace Orchestra\Http;

use Orchestra\Support\Transformer as BaseTransformer;

abstract class Transformer extends BaseTransformer
{
    use Concerns\Transformable;

    /**
     * Invoke the transformation.
     *
     * @param  mixed  $instance
     *
     * @return mixed
     */
    public static function with($instance, array $options = [])
    {
        return (new static())->options($options)->handle($instance);
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
