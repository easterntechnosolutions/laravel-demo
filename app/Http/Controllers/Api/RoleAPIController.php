<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\DataTrueResource;
use Illuminate\Support\Facades\Cache;
use App\Exports\RoleExport;
use App\Imports\RoleImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessPodcast;



class RoleAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('role.all', function () use($request){
                $role = new Role();
                $query = \App\Models\User::commonFunctionMethod(Role::select($role->light),$request,true);
                return new RoleCollection(RoleResource::collection($query),RoleResource::class);

            });
        }
        else{
            $query = User::commonFunctionMethod(Role::class,$request);
            return new RoleCollection(RoleResource::collection($query),RoleResource::class);

        }
    }

    /**
     * Show the form for creating a new resource.
     * Add Role
     * @param RoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->all());
        ProcessPodcast::dispatch($role);

        return User::GetMessage(new RoleResource($role), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
    	if (is_null($role)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new RoleResource($role);
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
     * @param  RoleUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $data = $request->all();       
        $role->update($data);        
        return User::GetMessage(new RoleResource($role), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if($role->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $role->delete();
       return new DataTrueResource($role,config('constants.messages.delete_success'));
    }

    public function deleteAll(Request $request,Role $role)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                Role::whereIn('id', $ids)->get()->each(function($role) {                  
                    $role->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }

    /**
     * Export Role Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function export(Request $request)
     {
        return Excel::download(new RoleExport($request), 'role_' . config('constants.file.name') . '.csv');
     }
     /**
     * Import Role Data
     * @param CsvRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function import(CsvRequest $request){
        return User::importBulk($request,new RoleImport(),'role','import/role/');

     }
}
