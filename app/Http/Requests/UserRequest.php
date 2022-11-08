<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        return [
            'name'=>'required|max:255',
            'email'=>'required|max:255|email',
            'password'=>'required|min:6|max:255',
            'dob'=>'required|date_format:Y-m-d', 
            'joining_date'=>'required', 
            'joining_time'=>'required|date_format:h:i:s A',
            'role_id'=>'required|exists:roles,id,deleted_at,NULL', 
            'phone_no'=>'required|digits:10', 
            'address'=>'required', 
            'country_id'=>'required', 
            'state_id'=>'required', 
            'city_id'=>'required', 
            'hobby_id'=>'required', 

            //'expiry_datetime'=>'required', 
            //'profile'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'gender'=>'required|in:0,1', 
            'status'=>'required|in:0,1',   
            'user_galleries'=>'required|array|max:5', 
            'user_galleries.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'user_pictures'=>'required|array|max:5', 
            'user_pictures.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];
        /*return [
            'name'=>'required|unique:users,name,NULL,id,deleted_at,NULL|max:191', 
            'email'=>'required|max:191|email', 
            'password'=>'required|min:6|max:191', 
            'role_id'=>'required|exists:roles,id,deleted_at,NULL', 
            'dob'=>'required|date_format:Y-m-d', 
            'joining_date'=>'required', 
            'joining_time'=>'required|date_format:h:i:s A', 
            'expiry_datetime'=>'required', 
            'profile'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'gender'=>'required|in:0,1', 
            'status'=>'required|in:0,1', 
            'user_galleries'=>'required|array|max:5', 
            'user_galleries.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096', 
            'user_pictures'=>'required|array|max:5', 
            'user_pictures.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            
        ];*/
    }
}
