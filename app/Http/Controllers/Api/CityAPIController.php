<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\User;
use App\Http\Requests\CityRequest;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\CityUpdateRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CityCollection;
use App\Http\Resources\DataTrueResource;
use Illuminate\Support\Facades\Cache;
use App\Exports\CityExport;
use App\Imports\CityImport;
use Maatwebsite\Excel\Facades\Excel;
class CityAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('city.all', function () use($request){
                $city = new City();
                $query = User::commonFunctionMethod(City::select($city->light),$request,true);
                return new CityCollection(CityResource::collection($query),CityResource::class);

            });
        }
        else{
            $query = User::commonFunctionMethod(City::class,$request);
            return new CityCollection(CityResource::collection($query),CityResource::class);
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
     * @param  CityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $city = City::create($request->all());
        return User::GetMessage(new CityResource($city), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::find($id);
    	if (is_null($city)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new CityResource($city);
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
     * @param  CityUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityUpdateRequest $request, City $city)
    {
        $data = $request->all();       
        $city->update($data);        
        return User::GetMessage(new CityResource($city), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if($city->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $city->delete();
        return new DataTrueResource($city,config('constants.messages.delete_success'));
    }
    public function deleteAll(Request $request,City $city)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                City::whereIn('id', $ids)->get()->each(function($city) {                  
                    $city->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }
    /**
     * Export City Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
       return Excel::download(new CityExport($request), 'city_' . config('constants.file.name') . '.csv');
    }
    /**
    * Import City Data
    * @param CsvRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function import(CsvRequest $request){
       return User::importBulk($request,new CityImport(),'city','import/city/');

    }
}
