<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Traits\Scopes;
use App\Models\City;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Traits\CreatedbyUpdatedby;
class CityImport implements ToCollection,WithStartRow
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
            'name'=>'required|max:255|unique:cities,name,NULL,id,deleted_at,NULL', 
            'state_id'=>'required|max:255', 
        ];
    }
    public function validationMessages()
    {
        return [
            '0.required'=>trans('The name is required.'), 
            '0.max'=>trans('The name may not be greater than 255 characters.'), 
            '0.unique'=>trans('The name has already been taken.'), 
            '1.max'=>trans('The guard_name may not be greater than 255 characters.'), 
            '1.required'=>trans('The country id is required.'), 

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
                $state = State::create([
                   'name'=>isset($col[0])?$col[0]:'',  
                    'state_id'=>isset($col[1])?$col[1]:'', 
                    'created_by'=>isset($col[2])?$col[2]:'', 
                    'updated_by'=>isset($col[3])?$col[3]:'',  
                    'deleted_by'=>isset($col[4])?$col[4]:'', 
                ]);
                
                
                
                $this->rows++;
            }
        }
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
}
