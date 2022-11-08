<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\DataTrueResource;
use App\Traits\CreatedbyUpdatedby;
use App\Traits\Scopes;
use App\Traits\UploadTrait;
use App\Http\Resources\UserPictureResource;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class UserPicture extends Model
{
    use SoftDeletes, Scopes, CreatedbyUpdatedby, HasFactory, UploadTrait;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'user_id', 'picture', 'picture_original', 'picture_thumbnail' ];

    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [  ];

    /**
     * Log Activity relationships array
     *
     * @var array
     */
    public $log_relations = [  ];

    /**
     * Lightweight response variable
     *
     * @var array
     */
    public $light = [ 'id', 'user_id', 'picture' ];

    /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [ 'users' ];

    /**
     * @var array
     */
    public $sortable = [ 'user_pictures.created_at', 'user_pictures.id',  ];

    /**
     * @var array
     */
    public $foreign_sortable = [ 'user_id' ];

    /**
     * @var array
     */
    public $foreign_table = [ 'users' ];

    /**
     * @var array
     */
    public $foreign_key = [ 'name' ];

    /**
     * @var array
     */
    public $foreign_method = [ 'user' ];

    /**
     * @var array
     */
    public $type_sortable = [  ];

    /**
     * @var array
     */
    public $type_enum = [
            
    ];

    /**
     * @var array
     */
    public $type_enum_text = [
            
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [  ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

            'id'=>'string', 
            'user_id'=>'string', 
            'picture'=>'string', 
            'picture_original'=>'string', 
            'picture_thumbnail'=>'string'

    ];

    /**
     * @param $value
     * @return mixed
     */
    public function getPictureAttribute($value) {
        if($this->is_file_exists($value))
            return \Illuminate\Support\Facades\Storage::url($value);
        else
            return asset(config('constants.image.default_img'));
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getPictureThumbnailAttribute($value) {
        if($this->is_file_exists($value))
            return \Illuminate\Support\Facades\Storage::url($value);
        else
            return asset(config('constants.image.default_img'));
    }
}
