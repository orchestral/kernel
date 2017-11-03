<?php

namespace Orchestra\Http\Resources\Json;

use Illuminate\Http\Resources\Json\Resource as BaseResource;

class Resource extends BaseResource
{
    use Tranformable;

    /**
     * Create new anonymous resource collection.
     *
     * @param  mixed  $resource
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return new AnonymousResourceCollection($resource, get_called_class());
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
        if (is_null($transformed = $this->toArrayUsingTransformer($request))) {
            return parent::toArray($request);
        }

        return $transformed;
    }
}
