<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\User;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\CsvRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\DataTrueResource;
use Illuminate\Support\Facades\Cache;
use App\Exports\CountryExport;
use App\Imports\CountryImport;
use Maatwebsite\Excel\Facades\Excel;
class CountryAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('country.all', function () use($request){
                $country = new Country();
                $query = User::commonFunctionMethod(Country::select($country->light),$request,true);
                return new CountryCollection(CountryResource::collection($query),CountryResource::class);

            });
        }
        else{
            $query = User::commonFunctionMethod(Country::class,$request);
            return new CountryCollection(CountryResource::collection($query),CountryResource::class);
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
     * @param  CountryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        $country = Country::create($request->all());
        return User::GetMessage(new CountryResource($country), config('constants.messages.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::find($id);
    	if (is_null($country)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new CountryResource($country);
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
     * @param  CountryUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $data = $request->all();       
        $country->update($data);        
        return User::GetMessage(new CountryResource($country), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        if($country->id == config('constants.system_role_id')){
            return User::GetError(config('constants.messages.admin_role_delete_error'));
        }
        $country->delete();
       return new DataTrueResource($country,config('constants.messages.delete_success'));
    }
    public function deleteAll(Request $request,Country $country)
    {
        if(!empty($request->id)) {
            $ids = explode(",", $request->id);
    
                if (in_array(config('constants.system_role_id'), $ids)){
                    return User::GetError(config('constants.messages.admin_role_delete_error'));
                }
                Country::whereIn('id', $ids)->get()->each(function($country) {                  
                    $country->delete();
                    });
                return new DataTrueResource(true,config('constants.messages.delete_success'));
            }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }
    /**
     * Export Country Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
       return Excel::download(new CountryExport($request), 'country_' . config('constants.file.name') . '.csv');
    }
    /**
    * Import Country Data
    * @param CsvRequest $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function import(CsvRequest $request){
       return User::importBulk($request,new CountryImport(),'country','import/country/');

    }
}
