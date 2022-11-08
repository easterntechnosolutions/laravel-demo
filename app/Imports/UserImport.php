<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Traits\CreatedbyUpdatedby;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Traits\Scopes;
use App\Models\User;
use App\Models\Role;

class UserImport implements ToCollection, WithStartRow
{
    use Scopes,CreatedbyUpdatedby;
    private $errors = [];
    private $rows = 0;

    public function startRow(): int
    {
        return 2;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function rules(): array
    {
        return [
            '0'=>'required|unique:users,name,NULL,id,deleted_at,NULL|max:191', 
            '1'=>'required|max:191|email', 
            '2'=>'required|min:6|max:191', 
            '3'=>'required|exists:roles,name,deleted_at,NULL', 
            '4'=>'required|date_format:Y-m-d', 
            '5'=>'required', 
            '6'=>'required|date_format:h:i:s A', 
            //'7'=>'required', 
            //'8'=>'required', 
            '7'=>'required|in:0,1', 
            '8'=>'required|in:0,1'
        ];
    }

    public function validationMessages()
    {
        return [
            '0.required'=>trans('The name is required.'), 
            '0.unique'=>trans('The name has already been taken.'), 
            '0.max'=>trans('The name may not be greater than 191 characters.'), 
            '1.required'=>trans('The email is required.'), 
            '1.max'=>trans('The email may not be greater than 191 characters.'), 
            '1.email'=>trans('The email is invalid.'), 
            '2.required'=>trans('The password is required.'), 
            '2.min'=>trans('The password must be at least 6 characters.'), 
            '2.max'=>trans('The password may not be greater than 191 characters.'), 
            '3.required'=>trans('The role_id is required.'), 
            '3.exists'=>trans('The selected role_id is invalid.'), 
            '4.required'=>trans('The dob is required.'), 
            '4.date_format'=>trans('The dob does not match the format Y-m-d.'), 
            '5.required'=>trans('The joining_date is required.'), 
            '6.required'=>trans('The joining_time is required.'), 
            '6.date_format'=>trans('The joining_time does not match the format h.'), 
            //'7.required'=>trans('The expiry_datetime is required.'), 
            //'8.required'=>trans('The profile is required.'), 
            '7.required'=>trans('The gender is required.'), 
            '7.in'=>trans('The gender is invalid.'), 
            '8.required'=>trans('The status is required.'), 
            '8.in'=>trans('The status is invalid.')
        ];
    }

    public function validateBulk($collection){
        $i=1;
        foreach ($collection as $col) {
            $i++;
            $errors[$i] = ['row' => $i];

            $validator = Validator::make($col->toArray(), $this->rules(), $this->validationMessages());
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                         $errors[$i]['error'][] = $error;
                    }
                }

                $this->errors[] = (object) $errors[$i];
            }
        }
        return $this->getErrors();
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $error = $this->validateBulk($collection);
        if($error){
            return;
        }else {
            foreach ($collection as $col) {
                $user = User::create([
                   'name'=>isset($col[0])?$col[0]:'', 
            'email'=>isset($col[1])?$col[1]:'',
            'password'=>isset($col[2])?bcrypt($col[2]):'', 
            'role_id'=>isset($col[3])?Role::where('name',$col[3])->value('id'):'', 
            'dob'=>isset($col[4])?$col[4]:'', 
            'joining_date'=>isset($col[5])?$col[5]:'', 
            'joining_time'=>isset($col[6])?$col[6]:'', 
            //'expiry_datetime'=>$col[7], 
            'gender'=>isset($col[7])?$col[7]:'', 
            'status'=>isset($col[8])?$col[8]:''
                ]);
                
                /*if($col[8]) {
                    $filepath = 'user/' . $user->id . '/' . $col[8];
                    $user->profile = $filepath;
                    $user->save();
                }
                
                
                $multipleFile = explode('|', $col[11]);
                if($multipleFile) {
                    foreach ($multipleFile as $file) {
                        $realPath = 'user/' . $user->id . '/' .'user_galleries/' . $file;
                        \App\Models\UserGallery::create([
                            'user_id' => $user->id,
                            'gallery' => $realPath,
                            'gallery_original' => $file,
                            'gallery_thumbnail' => $realPath
                        ]);
                    }
                }
            
                $multipleFile = explode('|', $col[12]);
                if($multipleFile) {
                    foreach ($multipleFile as $file) {
                        $realPath = 'user/' . $user->id . '/' .'user_pictures/' . $file;
                        \App\Models\UserPicture::create([
                            'user_id' => $user->id,
                            'picture' => $realPath,
                            'picture_original' => $file,
                            'picture_thumbnail' => $realPath
                        ]);
                    }
                }*/
                $this->rows++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
