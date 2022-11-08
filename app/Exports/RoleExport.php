<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Role;

class RoleExport implements FromCollection,WithHeadings
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
            'Guard name', 
            'Landing page'
        ];
    } 

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  \App\Models\User::commonFunctionMethod(Role::select('roles.id', 
            'roles.name', 
            'roles.guard_name', 
            'roles.landing_page'),
            $this->request, true, null, null, true);
    }
}
