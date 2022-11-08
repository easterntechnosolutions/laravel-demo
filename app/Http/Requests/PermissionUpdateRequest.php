<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PermissionUpdateRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $urlArr = explode("/", $request->path());
        $id = end($urlArr);
        
        return [
            'name'=>'required|max:255|unique:permissions,name,' . $id . ',id,deleted_at,NULL', 
            'guard_name'=>'required|max:255', 
            'label'=>'required|max:255'
            
        ];
    }
}
