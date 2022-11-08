<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'permissions' => $this->permissions,
            'authorization' => $this->authorization,
            'id'=>$this->id, 
            'name'=>$this->name, 
            'email'=>$this->email, 
            'role_id'=>$this->role_id, 
            //'role'=>new RoleResource($this->role), 
            'dob'=>$this->dob, 
            'joining_date'=>$this->joining_date, 
            'joining_time'=>$this->joining_time, 
            'expiry_datetime'=>$this->expiry_datetime, 
            'profile'=>$this->profile, 
            'profile_original'=>$this->profile_original, 
            'profile_thumbnail'=>$this->profile_thumbnail, 
            'gender'=>$this->gender, 
            'gender_text'=>config('constants.user.gender.'.$this->gender), 
            'status'=>$this->status, 
            'status_text'=>config('constants.user.status.'.$this->status), 
            'email_verified_at'=>$this->email_verified_at, 
            /*'user_galleries'=>UserGalleryResource::collection($this->user_galleries), 
            'user_pictures'=>UserPictureResource::collection($this->user_pictures),
            'sample_excels'=>array([
                'sample_user' => asset('samples/user.csv'),
                'sample_color' => asset('samples/color.csv'),
                'sample_product' => asset('samples/product.csv'),
                'sample_supplier' => asset('samples/supplier.csv'),
                'sample_brand' => asset('samples/brand.csv'),
            ]),*/
        ];
    }
}
