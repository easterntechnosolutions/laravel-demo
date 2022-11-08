<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\DataTrueResource;
use App\Models\Role;
use App\Models\Permission;
use Hash;
class LoginAPIController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return User::GetError(config('constants.messages.user.invalid'));
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if($user != null){
            //get User Permission and save permission in token
            $role = Role::findorfail($user->role_id);//get role details
            $token->scopes = $user->role->permissions->pluck('name')->toArray();
            $token->save();
            $user->permissions = Permission::getPermissions($role);
            $user->authorization = $tokenResult->accessToken;
            User::addOrchangeLastLoginTime($user->id); // Add/Change last login time
            return new LoginResource($user);
        }else{
            return User::GetError("No User found.");
        }
    }
    /**
     * change password functionality.
     *
     * @param ChangePasswordRequest $request
     * @return DataTrueResource|\Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        //get all updated data.
        $data = $request->all();
        $masterUser = User::where('email', $request->user()->email)->first();
        if (Hash::check($data['old_password'], $masterUser->password)) {
            $masterData['password'] = bcrypt($data['new_password']);
            //update user password in master user table
            if ($masterUser->update($masterData))
                return new DataTrueResource($masterUser,config('constants.messages.password_changed'));
            else
                return User::GetError(config("constants.messages.something_wrong"));
        }
        else
            return User::GetError(config("constants.messages.invalid_old_password"));

    }
    /**
     * Logout User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function logout(Request $request) {
        $user = Auth::user();
        $user->tokens()->delete();
        //Auth::logout();
        return response()->json('You have been Successfully logged out!');
    }
}
