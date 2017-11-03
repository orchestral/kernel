<?php

namespace Orchestra\Http\Resources\Json;

use Orchestra\Support\Transformer;
use Illuminate\Http\Resources\Json\Resource as BaseResource;

class Resource extends BaseResource
{
    /**
     * Transformer namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Transformer name.
     *
     * @var string|null
     */
    protected $transformer;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  string|null  $transformer
     * @param  string|null  $version
     */
    public function __construct($resource, $transformer = null, $version = null)
    {
        parent::__construct($resource);

        $this->transformer = ! is_null($transformer) ? $transformer : class_basename($resource);
        $this->version = $version;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $transformer = app('orchestra.http.version')->resolve(
            $this->namespace, $this->version, $this->transformer
        );

        if (class_exists($transformer, false)) {
            $transformer = new $transformer($request);

            if ($transformer instanceof Transformer) {
                return $transformer->handle($instance);
            }
        }

        return parent::toArray($request);
    }
}
