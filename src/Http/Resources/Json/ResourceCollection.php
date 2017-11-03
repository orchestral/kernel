<?php

namespace Orchestra\Http\Resources\Json;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResource;

class ResourceCollection extends BaseResource
{
    use Transformable;

    /**
     * Transform the resource into a JSON array.
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

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        //return $this->resource instanceof AbstractPaginator
                    // ? (new PaginatedResourceResponse($this))->toResponse($request)
                    //: parent::toResponse($request);
    }
}
