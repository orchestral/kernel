<?php

namespace Orchestra\Http\Resources\Json;

use Illuminate\Http\Resources\Json\ResourceCollection as BaseResource;

class ResourceCollection extends BaseResource
{
    use Transformable;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed
     */
    public function toArray($request)
    {
        if (method_exists($request, 'version')) {
            $this->version($request->version());
        }

        if (is_null($transformed = $this->toArrayUsingTransformer($request))) {
            return parent::toArray($request);
        }

        return $transformed;
    }
}
