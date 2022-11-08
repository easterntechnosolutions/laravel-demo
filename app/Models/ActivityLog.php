<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'log_name', 'description', 'ip_address', 'subject_type', 'subject_id', 'causer_type', 'causer_id', 'response_type', 'properties' ];

    /**
     * Lightweight response variable
     *
     * @var array
     */
    public $light = [ 'id', 'log_name', 'description' ];
    /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [];

    /**
     * @var array
     */
    public $sortable = [ 'created_at', 'log_name', 'description'];

    /**
     * @var array
     */
    public $foreign_sortable = [ 'causer_id' ];
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
    public $foreign_method = [ 'causer' ];

    /**
     * @var array
     */
    public $type_sortable = [];

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
        'log_name'=>'string',
        'description'=>'string',
        'ip_address'=>'string',
        'subject_type'=>'string',
        'subject_id'=>'string',
        'causer_type'=>'string',
        'causer_id'=>'string',
        'response_type'=>'string',
        'properties'=>'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causer() {
        return $this->belongsTo(\App\Models\User::class,'causer_id');
    }
}
