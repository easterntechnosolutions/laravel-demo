<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Country;
use App\Models\User;
class CountryExport implements FromCollection,WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function headings():array{
        return[
            'Id', 
            'Name'
        ];
    } 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  User::commonFunctionMethod(Country::select('countries.id', 
            'countries.name'
        ),
            $this->request, true, null, null, true);
    }
}
