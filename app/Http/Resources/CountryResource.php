<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($request->get('is_light',false)){
            return array_merge($this->attributesToArray(), $this->relationsToArray());
        }
        return [

            'id'=>$this->id, 
            'name'=>$this->name, 
        ];
    }
}
