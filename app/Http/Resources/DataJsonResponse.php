<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataJsonResponse extends JsonResource
{
    /**
     * The custom resource instance.
     *
     * @var mixed
     */
    public $customResource;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  mixed  $customResource
     * @return void
     */
    public function __construct($resource, $customResource)
    {
        parent::__construct($resource);
        $this->customResource = $customResource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $x = $this->customResource;

        if($request->get('is_light',false))
            return [
                'message' => "",
                'data' => $this->collection->transform(function($item) use ($x){
                    return new $x($item);
                })
            ];


        return [
            'current_page' => $this->currentPage(),
            'message' => "",
            'total' => $this->total(),
            'data' => $this->collection->transform(function($item) use ($x){
                return new $x($item);
            }),
            'first_page_url' => url($request->path()).'?page=1',
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => url($request->path()).'?page='.$this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => url($request->path()),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
        ];
    }
}
