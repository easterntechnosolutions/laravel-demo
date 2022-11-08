<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
class ForgotPasswordAPIController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    protected function validateEmail()
    {
        return [
            'email' => 'required|email|max:191|exists:users,email,deleted_at,NULL'
        ];
    }

    /**
     * Forgot password reset link success response
     * @param Request $request
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request,$response)
    {
        return response()->json(['message' => config('constants.messages.forgotpassword_success'), 'data' => ''],config('constants.validation_codes.ok'));
    }

    /**
     * Forgot password reset link fail response
     *
     * @param Request $request
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return User::GetError(trans($response));
    }
}
