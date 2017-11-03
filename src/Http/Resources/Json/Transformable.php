<?php

namespace Orchestra\Http\Resources\Json;

trait Transformable
{
    /**
     * Transformer name.
     *
     * @var string|null
     */
    protected $transformer;

    /**
     * Version code.
     *
     * @var string|null
     */
    protected $version;

    /**
     * Set transformer name.
     *
     * @param  string|null  $transformer
     *
     * @return $this
     */
    public function transformer($transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Set version.
     *
     * @param  string|null  $version
     *
     * @return $this
     */
    public function version($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArrayUsingTransformer($request)
    {
        $transformer = $this->resolveTransformer();

        if (class_exists($transformer, false)) {
            $service = new $transformer($request);

            if ($service instanceof Transformer) {
                return $service->fromResource($this)->handle($this->instance);
            }
        }
    }

    /**
     * Resolve transformer.
     *
     * @return string
     */
    protected function resolveTransformer()
    {
        $transformer = $this->transformer ?? class_basename($this->instance);

        return app('orchestra.http.version')->resolve(
            $this->getNamespace(), $this->version, $transformer
        );
    }

    /**
     * Get namespace.
     *
     * @return string
     */
    abstract protected function getNamespace();
}
