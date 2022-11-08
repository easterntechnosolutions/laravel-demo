<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Traits\Scopes;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Traits\CreatedbyUpdatedby;
class CountryImport implements ToCollection
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
            'name'=>'required|max:191|unique:countries,name,NULL,id,deleted_at,NULL', 
        ];
    }
    public function validationMessages()
    {
        return [
            '0.required'=>trans('The name is required.'), 
            '0.max'=>trans('The name may not be greater than 255 characters.'), 
            '0.unique'=>trans('The name has already been taken.'), 
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
                $role = Role::create([
                   'name'=>isset($col[0])?$col[0]:'', 
                    'created_by'=>isset($col[1])?$col[1]:'', 
                    'updated_by'=>isset($col[2])?$col[2]:'', 
                    'deleted_by'=>isset($col[3])?$col[3]:'', 
                ]);
                
                
                
                $this->rows++;
            }
        }
    }
}
