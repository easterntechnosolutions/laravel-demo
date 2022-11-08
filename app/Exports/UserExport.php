<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection,WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function headings():array
    {
        return[
            'Id', 
            'Name', 
            'Email', 
            'Role name', 
            'Dob', 
            'Joining_date', 
            'Joining_time', 
            'Expiry_datetime', 
            'Profile', 
            'Gender', 
            'Status'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  \App\Models\User::commonFunctionMethod(User::select('users.id', 
            'users.name', 
            'users.email', 
            \Illuminate\Support\Facades\DB::raw('(SELECT name from roles WHERE id = users.role_id) AS role_name'), 
            'users.dob', 
            'users.joining_date', 
            'users.joining_time', 
            'users.expiry_datetime', 
            'users.profile', 
            \Illuminate\Support\Facades\DB::raw('(CASE WHEN gender = "'.config('constants.user.gender_enum.female'). '" THEN "' . config('constants.user.gender.0').'" WHEN gender = "'.config('constants.user.gender_enum.male'). '" THEN "' . config('constants.user.gender.1').'" ELSE ""  END) AS gender'), 
            \Illuminate\Support\Facades\DB::raw('(CASE WHEN status = "'.config('constants.user.status_enum.inactive'). '" THEN "' . config('constants.user.status.0').'" WHEN status = "'.config('constants.user.status_enum.active'). '" THEN "' . config('constants.user.status.1').'" ELSE ""  END) AS status')),
            $this->request, true, null, null, true);
    }
    
}
