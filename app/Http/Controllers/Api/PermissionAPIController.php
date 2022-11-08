<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use App\Http\Resources\DataTrueResource;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use App\Exports\PermissionExport;
use App\Imports\PermissionImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Models\PermissionRole;
use App\Models\Role;

class PermissionAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){

            return Cache::rememberForever('permission.all', function () use($request){
                $permission = new Permission();
                $query = User::commonFunctionMethod(Permission::select($permission->light),$request,true);
                return new PermissionCollection(PermissionResource::collection($query),PermissionResource::class);
            });
        }
        else
            $query = User::commonFunctionMethod(Permission::class,$request);

        return new PermissionCollection(PermissionResource::collection($query),PermissionResource::class);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PermissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $permission = Permission::create($request->all());
        return User::GetMessage(new PermissionResource($permission), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::find($id);
    	if (is_null($permission)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new PermissionResource($permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PermissionUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $data = $request->all();       
        $permission->update($data);        
        return User::GetMessage(new PermissionResource($permission), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        if($permission->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $permission->delete();
        return new DataTrueResource($permission,config('constants.messages.delete_success'));
    }
    public function deleteAll(Request $request,Permission $permission)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                Permission::whereIn('id', $ids)->get()->each(function($permission) {                  
                    $permission->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }
    /**
     * Export Permission Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
       return Excel::download(new PermissionExport($request), 'permission_' . config('constants.file.name') . '.csv');
    }
    /**
    * Import Permission Data
    * @param CsvRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function import(CsvRequest $request){
        return User::importBulk($request,new PermissionImport(),'permission','import/permission/');
 
    }
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
