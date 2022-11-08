<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Hash;
class ChangePasswordRequest extends FormRequest
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
            'old_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The Old password is incorrect.'));
                }
            }],
            'new_password' => 'required|required_with:confirm_password|same:confirm_password|min:6|max:255|different:old_password',
            'confirm_password' => 'required|min:6|max:255',
        ];
    }
}
