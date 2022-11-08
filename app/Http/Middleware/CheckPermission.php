<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Str;
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //get controllerclassname@method
        $actionName = class_basename($request->route()->getActionname());
        //separate it from '@' character
        $actionName = explode("@", $actionName);
        //get module name from Controller class e.g. ClientsAPIController -> client
        if (Str::contains($actionName[0], 'APIController'))
            $moduleOrg = str_replace("APIController", "", $actionName[0]);
        else
            $moduleOrg = str_replace("sAPIController", "", $actionName[0]);
        $module = Str::lower($moduleOrg);
        $action = $actionName[1];
        $permission = $action . '-' . Str::plural($module);
        $excluded_permissions = [ 
            'changePassword-logins',
            'index-importcsvlogs',
            'show-importcsvlogs',
            'logout-logins',
            'getActivities-users',
            'checkEmail-users',
            'batchRequest-users'
        ];
        if ($request->path() == 'api/v1/batch-request' || $request->path() == 'api/v1/auth-batch-request')
            return $next($request);
        if (in_array($permission, $excluded_permissions))
            return $next($request);
        else if ($request->user()->tokenCan($permission)) //check user permission with his token
            return $next($request); //redirect to requested uri if permitted
        else //reject the requested uri
        {
            $excluded_related_permissions = []; // harcode permission array
            $error = 0;
            if ($action == "index" || in_array($permission, $excluded_related_permissions)) {
                $model = 'App\Models\\' . $moduleOrg; //get module name
                $modelObj = new $model();
                if (!is_null($modelObj) && !empty($modelObj->related_permission)) {
                    $relatedPermissions = $modelObj->related_permission; //  //get related_permission from model e.g. Move_type -> related_permission
                    foreach ($relatedPermissions as $relatedPermission) {
                        $actionPermission = explode("-", $relatedPermission); //separate it from '-' character
                        if (isset($actionPermission[1])) {
                            $permission = $relatedPermission;
                        } else {
                            $permission = "index-" . $actionPermission[0];
                        }
                        if ($request->user()->tokenCan($permission))
                            return $next($request);
                    }
                }
                $error = 1;
            } else {
                $error = 1;
            }
            if ($error == 1) {
                return response()->json(
                    ['message' => config('constants.permission.user_has_not_permission'), 'errors' => (object)[]],
                    config('constants.validation_codes.forbidden')
                );
            }
        }
    }
}
