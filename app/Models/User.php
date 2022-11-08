<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use App\Traits\Scopes;
use App\Traits\UploadTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserGallery;
use Carbon\Carbon;
use App\Models\UserPicture;
use App\Models\UserHobby;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Scopes, UploadTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'joining_date',
        'joining_time',
        'expiry_datetime',
        'role_id',
        'profile',
        'profile_original',
        'profile_thumbnail',
        'gender',
        'status',
        'last_login_time',
        'last_seen_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'role_id',
        'country_id',
        'state_id',
        'city_id',
        'phone_no',
        'address'
    ];
    /**
    * Activity log array
    *
    * @var array
    */
    public $activity_log = [ 'id', 'name', 'email', 'role->name', 'dob', 'joining_date', 'joining_time', 'expiry_datetime', 'profile', 'gender', 'status', 'email_verified_at', 'user_galleries->gallery', 'user_pictures->picture', 'last_login_time', 'last_seen_at' ];
    /**
    * Log Activity relationships array
    *
    * @var array
    */
   public $log_relations = [ 'role', 'user_galleries', 'user_pictures', 'country', 'state', 'city', 'user_hobbies' ];

    /**
    * Lightweight response variable
    *
    * @var array
    */
    public $light = [ 'id', 'name', 'email' ];
    /**
    * Related permission array
    *
    * @var array
    */
    public $related_permission = [  ];
    /**
    * @var array
    */
    public $sortable = [ 'users.created_at', 'users.id', 'name', 'email', 'dob', 'joining_date', 'joining_time', 'expiry_datetime' ];
    /**
    * @var array
    */
    public $foreign_sortable = [ 'role_id', 'country_id', 'state_id', 'city_id'];

    /**
        * @var array
        */
    public $foreign_table = [ 'roles', 'countries', 'states', 'cities'];

    /**
        * @var array
        */
    public $foreign_key = [ 'name', 'name', 'name', 'name' ];

    /**
        * @var array
        */
    public $foreign_method = [ 'role', 'country', 'state', 'city' ];

    /**
        * @var array
        */
    public $type_sortable = [ 'gender', 'status' ];

    /**
        * @var array
        */
    public $type_enum = [
                ['constants.user.gender_enum.female','constants.user.gender_enum.male'], 
                ['constants.user.status_enum.inactive','constants.user.status_enum.active']
    ];

    /**
        * @var array
        */
    public $type_enum_text = [
                ['constants.user.gender.0','constants.user.gender.1'], 
                ['constants.user.status.0','constants.user.status.1']
    ];

    /**
        * The attributes that should be mutated to dates.
        *
        * @var array
        */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'=>'string', 
        'name'=>'string', 
        'email'=>'string', 
        'role_id'=>'string', 
        'dob'=>'string', 
        'joining_date'=>'string', 
        'joining_time'=>'string', 
        'expiry_datetime'=>'string', 
        'profile'=>'string', 
        'profile_original'=>'string', 
        'profile_thumbnail'=>'string', 
        'gender'=>'string', 
        'status'=>'string', 
        'email_verified_at' => 'datetime',
        'role_id' => 'string',
        'country_id'=> 'string',
        'state_id' => 'string',
        'city_id' => 'string',
        'phone_no' => 'string',
        'address' => 'string'
    ];

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function role() {
        return $this->belongsTo(\App\Models\Role::class);
     }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function country() {
        return $this->belongsTo(\App\Models\Country::class);
     }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function state() {
        return $this->belongsTo(\App\Models\State::class);
     }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function city() {
        return $this->belongsTo(\App\Models\City::class);
     }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function user_galleries() {
       return $this->hasMany(UserGallery::class,'user_id');
    }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function user_pictures() {
        return $this->hasMany(UserPicture::class,'user_id');
     }
     /**
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function user_hobbies() {
        return $this->hasMany(UserHobby::class,'user_id');
    }
    public function userHobby()
    {
        return $this->belongsToMany(UserHobby::class,"user_hobbies","user_id","hobby_id");
    }

     /**
     *  Common Display Messsage Response.
     *
     * @param $resource
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function GetMessage($resource, $message){

        return response()->json([
            'message' => $message,
            'data' => $resource,
        ]);

    }
    /**
     * Common Display Error Message.
     *
     * @param $query
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function GetError($message){
        return response()->json(['message' => $message,'errors' => (object)[]], config('constants.validation_codes.unassigned'));
    }

    public function scopeCommonFunctionMethod($query, $model, $request, $preQuery = null, $tablename = null, $groupBy = null, $export_select = false, $no_paginate = false)
    {
        return $this->getCommonFunctionMethod($model, $request, $preQuery, $tablename , $groupBy , $export_select , $no_paginate);
    }

    public static function getCommonFunctionMethod($model, $request, $preQuery = null, $tablename = null, $groupBy = null, $export_select = false, $no_paginate = false)
    {
        if (is_null($preQuery)) {
            $mainQuery = $model::withSearch($request->get('search'), $export_select);
        } else {
            $mainQuery = $model->withSearch($request->get('search'), $export_select);
        }
        if ($request->filled('filter') != '')
            $mainQuery = $mainQuery->withFilter($request->get('filter'));
        if (!is_null($groupBy))
            $mainQuery = $mainQuery->groupBy($groupBy);
        if ($no_paginate) {
            return $mainQuery->withOrderBy($request->get('sort'), $request->get('order_by'), $tablename, $export_select, $groupBy);
        } else {

            $mainQuery = $mainQuery->withOrderBy($request->get('sort'), $request->get('order_by'), $tablename, $export_select, $groupBy);

            if ($request->filled('per_page'))
                return $mainQuery->withPerPage($request->get('per_page'));
            else
                return $mainQuery->withPerPage($mainQuery->count());
        }
    }
    public function scopeAddOrChangeLastLoginTime($query, $id)
    {

        return $query->where('id', $id)->update([
            'last_login_time' => time(),
            'last_seen_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
