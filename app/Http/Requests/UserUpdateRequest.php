<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $urlArr = explode("/", $request->path());
        $id = end($urlArr);
        
        return [
            'name'=>'required|unique:users,name,' . $id . ',id,deleted_at,NULL|max:191', 
            'email'=>'required|max:191|email', 
            'role_id'=>'required|exists:roles,id,deleted_at,NULL', 
            'phone_no'=>'required|digits:10', 
            'address'=>'required', 
            'country_id'=>'required', 
            'state_id'=>'required', 
            'city_id'=>'required', 
            'hobby_id'=>'required',
            'dob'=>'required|date_format:Y-m-d', 
            'joining_date'=>'required', 
            'joining_time'=>'required|date_format:h:i:s A', 
            'expiry_datetime'=>'required', 
            'profile'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'gender'=>'required|in:0,1', 
            'status'=>'required|in:0,1', 
            'user_galleries'=>'nullable|array|max:5', 
            'user_galleries.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'user_pictures'=>'nullable|array|max:5', 
            'user_pictures.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
            
        ];
    }
}
