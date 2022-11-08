<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Scopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportCsvLog extends Model
{
    use HasFactory, Scopes, SoftDeletes;
    protected $table = 'import_csv_logs';
    /**
     * @var array
     */

    protected $fillable = ['filename','file_path','model_name','user_id','status','no_of_rows','error_log'];

    public $sortable=[
        'filename','file_path','model_name','error_log','no_of_rows'
    ];
     
    public $type_sortable = [
        'status',
    ];

    public $type_enum = [
        [
            'constants.import_csv_log.status_enum.0',
            'constants.import_csv_log.status_enum.1',
        ],
    ];

    public $type_enum_text = [
        [
            'constants.import_csv_log.status.0',
            'constants.import_csv_log.status.1',
        ],
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
        'id'=>'string',
        'filename'=>'string',
        'file_path'=>'string',
        'model_name'=>'string',
        'created_at'=>'string',
        'updated_at'=>'string'
    ];

    /**
     * @param $value
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getFilePathAttribute($value){
        if ($value == NULL){
            return "";
        }
        return url(config('constants.image.dir_path') . $value);
    }

}
