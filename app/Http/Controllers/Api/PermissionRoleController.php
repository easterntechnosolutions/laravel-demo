<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\Permission;

class PermissionRoleController extends Controller
{
    public function setUnsetPermission(Request $request)
    {
        $permissionRole = PermissionRole::where("role_id",$request->get("role_id"))
            ->where("permission_id",$request->get("permission_id"))->first();
        if($request->get("is_permission") == "1"){

            if(is_null($permissionRole)){

                PermissionRole::insert([
                    "role_id" => $request->get("role_id"),
                    "permission_id" => $request->get("permission_id"),
                    'is_permission' => $request->get('is_permission')
                ]);

            }

        }else{
            PermissionRole::where("role_id",$request->get("role_id"))
                ->where("permission_id",$request->get("permission_id"))->delete();

        }

        return response()->json(["message" => config('constants.messages.change_success'),"data" => true]);
    }
    public function getPermissions($role, $permission = null)
    {
        $isPermission = $role->permissions->pluck('id');// get all role permissions mappings
        dd($isPermission);
    }
}
