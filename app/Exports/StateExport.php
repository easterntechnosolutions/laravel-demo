<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use App\Models\State;

class StateExport implements FromCollection,WithHeadings
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
            'Country Name'
        ];
    } 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  \App\Models\User::commonFunctionMethod(State::select('states.id', 
            'states.name', 
            \Illuminate\Support\Facades\DB::raw('(SELECT name from countries WHERE id = states.country_id) AS country_name'), 
            ),
            $this->request, true, null, null, true);
    }
}
