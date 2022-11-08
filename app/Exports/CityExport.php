<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use App\Models\City;
class CityExport implements FromCollection,WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function headings():array{
        return[
            'Id', 
            'Name',
            'State Name'
        ];
    } 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  \App\Models\User::commonFunctionMethod(City::select('cities.id', 
            'cities.name', 
            \Illuminate\Support\Facades\DB::raw('(SELECT name from states WHERE id = cities.state_id) AS state_name'), 
            ),
            $this->request, true, null, null, true);
    }
}
