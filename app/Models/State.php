<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UploadTrait;
use App\Traits\Scopes;
use App\Models\City;
use App\Models\Country;

class State extends Model
{
    use HasFactory,Scopes, UploadTrait, SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'name', 'country_id'];
    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [ 'id', 'name', 'country_id'];
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
    public $light = [ 'id', 'name', 'country_id' ];
    /**
     * Related permission array
     *
     * @var array
     */
    public $related_permission = [ 'countries' ];
    /**
     * @var array
     */
    public $sortable = [ 'states.created_at', 'states.id', 'name', 'country_id'];
    /**
     * @var array
     */
    public $foreign_sortable = [ 'country_id' ];

    /**
     * @var array
     */
    public $foreign_table = [ 'countries' ];

    /**
     * @var array
     */
    public $foreign_key = [ 'name' ];

    /**
     * @var array
     */
    public $foreign_method = [ 'country' ];

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
        'country_id'=>'string', 
        'created_by'=>'string', 
        'updated_by'=>'string', 
        'deleted_by'=>'string'
    ];
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function country() {
        return $this->belongsTo(Country::class);
    }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function city() {
        return $this->hasMany(City::class,'state_id');
     }
}
