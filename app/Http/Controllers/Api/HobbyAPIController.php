<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hobby;
use App\Models\User;
use App\Http\Requests\HobbyRequest;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\HobbyUpdateRequest;
use App\Http\Resources\HobbyResource;
use App\Http\Resources\HobbyCollection;
use App\Http\Resources\DataTrueResource;
use Illuminate\Support\Facades\Cache;
use App\Exports\HobbyExport;
use App\Imports\HobbyImport;
use Maatwebsite\Excel\Facades\Excel;
class HobbyAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('hobby.all', function () use($request){
                $hobby = new Hobby();
                $query = \App\Models\User::commonFunctionMethod(Hobby::select($hobby->light),$request,true);
                return new HobbyCollection(HobbyResource::collection($query),HobbyResource::class);

            });
        }
        else{
            $query = User::commonFunctionMethod(Hobby::class,$request);
            return new HobbyCollection(HobbyResource::collection($query),HobbyResource::class);

        }
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
     * @param  HobbyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HobbyRequest $request)
    {
        $hobby = Hobby::create($request->all());
        return User::GetMessage(new HobbyResource($hobby), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hobby = Hobby::find($id);
    	if (is_null($hobby)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new HobbyResource($hobby);
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
     * @param  HobbyUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HobbyUpdateRequest $request, Hobby $hobby)
    {
        $data = $request->all();       
        $hobby->update($data);        
        return User::GetMessage(new HobbyResource($hobby), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hobby $hobby)
    {
        if($hobby->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $hobby->delete();
       return new DataTrueResource($hobby,config('constants.messages.delete_success'));
    }
    public function deleteAll(Request $request,Hobby $hobby)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                Hobby::whereIn('id', $ids)->get()->each(function($hobby) {                  
                    $hobby->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }
    /**
     * Export Hobby Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
       return Excel::download(new HobbyExport($request), 'hobby_' . config('constants.file.name') . '.csv');
    }
    /**
    * Import Hobby Data
    * @param CsvRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function import(CsvRequest $request){
       return User::importBulk($request,new HobbyImport(),'hobby','import/hobby/');

    }
}
