<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Permission;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
class PermissionExport implements FromCollection,WithHeadings
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
        return  User::commonFunctionMethod(Permission::select('permissions.id', 
            'permissions.name'),
            $this->request, true, null, null, true);
    }
}
