<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UploadTrait;
use App\Traits\Scopes;
use App\Models\State;
class City extends Model
{
    use HasFactory,Scopes, UploadTrait, SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'name', 'state_id'];
    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [ 'id', 'name', 'state_id'];
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
    public $light = [ 'id', 'name', 'state_id' ];
    /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [ 'states' ];
    /**
     * @var array
     */
    public $sortable = [ 'cities.created_at', 'cities.id', 'name', 'state_id'];
    /**
     * @var array
     */
    public $foreign_sortable = [ 'state_id' ];
    /**
     * @var array
     */
    public $foreign_table = [ 'states' ];

    /**
     * @var array
     */
    public $foreign_key = [ 'name' ];
    /**
     * @var array
     */
    public $foreign_method = [ 'state' ];

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
        'state_id'=>'string', 
        'created_by'=>'string', 
        'updated_by'=>'string', 
        'deleted_by'=>'string'
    ];
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function state() {
        return $this->belongsTo(State::class);
    }
}
