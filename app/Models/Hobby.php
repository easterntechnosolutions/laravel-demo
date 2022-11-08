<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Scopes;
use App\Traits\UploadTrait;
use App\Traits\CreatedbyUpdatedby;

class Hobby extends Model
{
    use HasFactory, SoftDeletes, Scopes, CreatedbyUpdatedby, UploadTrait;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'name'];
    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [ 'id', 'name' ];

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
    public $light = [ 'id', 'name' ];

     /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [ 'users' ];
    /**
     * @var array
     */
    public $sortable = [ 'hobbies.created_at', 'hobbies.id', 'name'];
    /**
     * @var array
     */
    public $foreign_sortable = [  ];

    /**
     * @var array
     */
    public $foreign_table = [  ];

    /**
     * @var array
     */
    public $foreign_key = [  ];

    /**
     * @var array
     */
    public $foreign_method = [  ];

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
        'name'=>'string', 
        'created_by'=>'string', 
        'updated_by'=>'string', 
        'deleted_by'=>'string'
];
}
