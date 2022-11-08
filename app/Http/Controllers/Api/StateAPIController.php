<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\User;
use App\Http\Requests\StateRequest;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\StateUpdateRequest;
use App\Http\Resources\StateResource;
use App\Http\Resources\StateCollection;
use App\Http\Resources\DataTrueResource;
use Illuminate\Support\Facades\Cache;
use App\Exports\StateExport;
use App\Imports\StateImport;
use Maatwebsite\Excel\Facades\Excel;
class StateAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('state.all', function () use($request){
                $state = new State();
                $query = User::commonFunctionMethod(State::select($state->light),$request,true);
                return new StateCollection(StateResource::collection($query),StateResource::class);

            });
        }
        else{
            $query = User::commonFunctionMethod(State::class,$request);
            return new StateCollection(StateResource::collection($query),StateResource::class);
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
     * @param  StateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StateRequest $request)
    {
        $state = State::create($request->all());
        return User::GetMessage(new StateResource($state), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $state = State::find($id);
    	if (is_null($state)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new StateResource($state);
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
     * @param  StateUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StateUpdateRequest $request, State $state)
    {
        $data = $request->all();       
        $state->update($data);        
        return User::GetMessage(new StateResource($state), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        if($state->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $state->delete();
       return new DataTrueResource($state,config('constants.messages.delete_success'));
    }
    public function deleteAll(Request $request,State $state)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                State::whereIn('id', $ids)->get()->each(function($state) {                  
                    $state->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }
    /**
     * Export State Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
       return Excel::download(new StateExport($request), 'state_' . config('constants.file.name') . '.csv');
    }
    /**
    * Import State Data
    * @param CsvRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function import(CsvRequest $request){
       return User::importBulk($request,new StateImport(),'state','import/state/');

    }
}
