<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserGalleryResource extends JsonResource
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
            'user_id'=>$this->user_id, 
            'gallery'=>$this->gallery, 
            'gallery_original'=>$this->gallery_original, 
            'gallery_thumbnail'=>$this->gallery_thumbnail
        ];
    }
}
