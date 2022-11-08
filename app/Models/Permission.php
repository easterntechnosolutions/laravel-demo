<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Scopes;
use App\Traits\UploadTrait;
use App\Traits\CreatedbyUpdatedby;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes, Scopes, UploadTrait, CreatedbyUpdatedby;
    /**
     * @var array
     */
    protected $fillable = [ 'id', 'name', 'label', 'guard_name'];
    /**
     * Activity log array
     *
     * @var array
     */
    public $activity_log = [ 'id', 'name', 'label', 'guard_name' ];
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
    public $related_permission = [  ];

    /**
     * @var array
     */
    public $sortable = [ 'permissions.created_at', 'permissions.id', 'name', 'label', 'guard_name' ];
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
            'label'=>'string', 
            'guard_name'=>'string', 
            'created_by'=>'string', 
            'updated_by'=>'string', 
            'deleted_by'=>'string'

    ];
    /**
     * @param $role
     * @return array - array of permission
     */
    public static function getPermissions($role, $permission = null)
    {
        $isPermission = $role->permissions->pluck("id")->toArray();// get all role permissions mappings
        $allPermission = [];

        $rootPermissions = Self::getPermissionByGuardName("root");// get permissions

        if (!$rootPermissions->isEmpty()) {// Check permissions is not empty
            foreach ($rootPermissions As $root) {

                $continue = true;// set flag for specific model response

                if($permission){// Check permission is not null

                    if($root["name"] != $permission->guard_name)
                        $continue = false;

                }

                if($continue){

                    $root = Self::commonPermissionCode($root, $isPermission);
                    $firstPermission = [];
                    $firstPermissions = Self::getPermissionByGuardName($root["name"]);// get permissions

                    if (!$firstPermissions->isEmpty()) {// Check permissions is not empty
                        foreach ($firstPermissions As $first) {

                            $first = Self::commonPermissionCode($first, $isPermission);
                            $name = explode("-",$first["name"]);
                            $first["name"] = $name[0];
                            $firstPermission[] = $first;
                        }
                    }

                    $root["sub_permissions"] = $firstPermission;
                    $allPermission[] = $root;

                }
            }
        }

        return $allPermission;
    }
    /**
     * This static method is used to get permissions by guardname
     *
     * @param $guardName
     * @return mixed
     */
    public static function getPermissionByGuardName($guardName)
    {
        return Permission::select("id","name","label","guard_name")
            ->where("guard_name",$guardName)
            ->orderBy("created_at","asc")
            ->get();
    }
    /**
     * This method is used for display name for permission and it's status
     *
     * @param $array
     * @param $isPermission
     * @return mixed
     */
    public static function commonPermissionCode($array,$isPermission)
    {
        $array["is_permission"] = config("constants.permission.has_not_permission");
        if(in_array($array["id"],$isPermission))
                $array["is_permission"] = config("constants.permission.has_permission");


        $name = str_replace("-", " ",$array["name"]);
        $name = str_replace("and", "&",$name);
        $name = str_replace("slash", "/",$name);
        $array["display_name"] = ucwords($name);
        return $array;
    }
}
