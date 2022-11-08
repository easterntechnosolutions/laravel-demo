<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email'=>$this->email, 
            'role_id'=>$this->role_id, 
            'role'=>new RoleResource($this->role), 
            'dob'=>$this->dob, 
            'joining_date'=>$this->joining_date, 
            'joining_time'=>$this->joining_time, 
            'expiry_datetime'=>$this->expiry_datetime, 
            'phone_no'=>$this->phone_no, 
            'address'=>$this->address, 
            'profile'=>$this->profile, 
            'profile_original'=>$this->profile_original, 
            'profile_thumbnail'=>$this->profile_thumbnail, 
            'gender'=>$this->gender, 
            'gender_text'=>config('constants.user.gender.'.$this->gender), 
            'status'=>$this->status, 
            'status_text'=>config('constants.user.status.'.$this->status), 
            'email_verified_at'=>$this->email_verified_at, 
            'user_galleries'=>UserGalleryResource::collection($this->user_galleries), 
            'user_pictures'=>UserPictureResource::collection($this->user_pictures),
            'user_hobbies'=>UserHobbyResource::collection($this->user_hobbies),
            'country_id'=>$this->country_id, 
            'country'=>new CountryResource($this->country),
            'state_id'=>$this->state_id, 
            'state'=>new StateResource($this->state),
            'city_id'=>$this->city_id, 
            'city'=>new CityResource($this->city),

        ];
    }
}
