<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use URL;

trait CreatedbyUpdatedby
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });
        static::created(function ($model) {
            $user = Auth::user();
            if ($user) {
                Cache::forget(Str::lower(class_basename($model)) . '.all'); // Forgot the model cache
                $reqData = Request::capture()->all();
                $response = Self::getModelWithRelationDetails($model, $reqData, 'created(');
                Self::createLog($user, $model, $response, config('constants.activity_log.response_type_enum.0'));
            }
        });
        static::updating(function ($model) {
            $user = Auth::user();
            if ($user) {
                Self::setSessionForResourceResponse($model);
                $model->updated_by = $user->id;
            }
        });
        static::updated(function ($model) {
            $user = Auth::user();
            if ($user) {
                Cache::forget(Str::lower(class_basename($model)) . '.all'); // Forgot the model cache
                $reqData = Request::capture()->all();
                $response = Self::getModelWithRelationDetails($model, $reqData, 'updated(', true);
                Self::createLog($user, $model, $response, config('constants.activity_log.response_type_enum.1'));
            }
        });
        static::deleting(function ($model) {
            $user = Auth::user();
            if ($user) {
                Self::setSessionForResourceResponse($model);
                $model->deleted_by = $user->id;
                $model->save();
            }
        });
        static::deleted(function ($model) {
            $user = Auth::user();
            if ($user) {

                Cache::forget(Str::lower(class_basename($model)) . '.all'); // Forgot the model cache
                $response = [
                    'description' => rtrim(Self::getDescription($model, session('model_log'), 'deleted('), "|"),
                    'properties' => [
                        'attributes' => session('model_log')
                    ],
                ];

                Self::createLog($user, $model, $response, config('constants.activity_log.response_type_enum.0'));
            }
        });
    }

    /**
     *  Create a activity log
     *
     * @param $user
     * @param $model
     * @param $response
     * @param $responseType
     * @param bool $fromIndexMethod
     */
    public static function createLog($user, $model, $response, $responseType, $fromIndexMethod = false)
    {
        $model = class_basename($model);
        $formatModel = \Illuminate\Support\Str::lower($model);

        $whereData = [
            'log_name' => $formatModel,
            'description' => $fromIndexMethod ? $response['description'] : $response['description'] . ')',
            'ip_address' => Request::capture()->ip(),
            'subject_type' => 'App\Models\\' . $model,
            'subject_id' => $fromIndexMethod ? null : $response['properties']['attributes']['id'],
            'causer_type' => 'App\Models\User',
            'causer_id' => $user ? $user->id : null,
            'response_type' => $responseType,
        ];

        $data = $whereData;
        $data['properties'] = json_encode($response['properties']);

        $activityLog = ActivityLog::where($whereData)->where('created_at', date('Y-m-d H:i:s'))->first();

        if (is_null($activityLog))
            ActivityLog::create($data);
    }

    /**
     *  Get the model detail with it's relationship details
     *
     * @param $model
     * @param $reqData
     * @param $description
     * @param bool $isUpdate
     * @return array
     */
    public static function getModelWithRelationDetails($model, $reqData, $description, $isUpdate = false)
    {
        $response = get_class($model)::where('id', $model->id)->with($model->log_relations)->first();
        $attributes = $response->getRawOriginal();
        $properties = [];

        foreach ($model->activity_log as $log) {
            $logArr = explode("->", $log);
            if (count($logArr) == 2) {
                $relation = $logArr[0];
                $fieldName = $logArr[1];

                if (isset($reqData[$relation]) && gettype($reqData[$relation]) == 'array' && !isset($reqData[$relation]['created_at'])) {

                    $className = '\App\Models\\' . Str::ucfirst(Str::singular($relation));

                    if (Self::getArrayValueIsInt($reqData[$relation])) {
                        $modelData = $className::whereIn('id', $reqData[$relation])->get();
                        $description .= implode(",", $modelData->pluck($fieldName)->toArray()) . '|';
                        $attributes[$relation] = Self::setPivotResponseInProperties($response, $modelData);
                    } else {
                        $description .= implode(",", $reqData[$relation]) . '|';
                        $attributes[$relation] = $reqData[$relation];
                    }
                } else {
                    if (!is_null($response->$relation) && isset($response->$relation->$fieldName) && !is_null($response->$relation->$fieldName)) {

                        $description .= ($response->$relation->$fieldName) . '|';
                        $attributes[$relation] = $response->$relation;
                    }
                }
            } else {
                $description .= ($response->$log) . '|';
            }
        }

        $properties['attributes'] = $attributes;
        if ($isUpdate) {
            $properties['changes'] = $model->getChanges();
            $properties['old'] = session('model_log');
        }

        return [
            'description' => rtrim($description, "|"),
            'properties' => $properties,
        ];
    }

    /**
     *  Get the status for array value is numeric or not
     *
     * @param $array
     * @return bool
     */
    public static function getArrayValueIsInt($array)
    {
        foreach ($array as $row) {

            if (!is_numeric($row))
                return false;
        }

        return true;
    }

    /**
     *  Set pivot object in pivot table response
     *
     * @param $response
     * @param $modelData
     * @return mixed
     */
    public static function setPivotResponseInProperties($response, $modelData)
    {
        if (!$modelData->isEmpty()) {

            foreach ($modelData as $row) {

                $row->pivot = [
                    Str::lower(class_basename($response)) . '_id' => $response->id,
                    Str::lower(class_basename($row)) . '_id' => $row->id,
                ];
            }
        }

        return $modelData;
    }

    /**
     *  Get the description for activity log specific to use when Delete operation is perform
     *
     * @param $model
     * @param $response
     * @param $description
     * @return string
     */
    public static function getDescription($model, $response, $description)
    {
        foreach ($model->activity_log as $log) {
            $logArr = explode("->", $log);
            if (count($logArr) == 2) {
                $relation = $logArr[0];
                $fieldName = $logArr[1];

                if (!is_null($response->$relation) && gettype($response->$relation) == 'object' && $relation == Str::plural($relation)) {
                    $relationFieldArr = $response->$relation->pluck($fieldName)->toArray();
                    $description .= implode(",", $relationFieldArr) . '|';
                } else {
                    if (!is_null($response->$relation)) {
                        $description .= ($response->$relation->$fieldName) . '|';
                    }
                }
            } else
                $description .= ($response->$log) . '|';
        }

        return rtrim($description, "|");
    }

    /**
     *  Set the session for model resource response
     *
     * @param $model
     */
    public static function setSessionForResourceResponse($model)
    {
        $response = get_class($model)::where('id', $model->id)->with($model->log_relations)->first();
        $modelName = str_replace("App\Models\\", "", '\App\Http\Resources\\' . get_class($model) . 'Resource');

        session(['model_log' => new $modelName($response)]);
    }

    /**
     *  Get the description for activity log to index method and export method only
     *
     * @param $queryStringArr
     * @param $realURI
     * @param $indexDescription
     * @return string
     */
    public static function getIndexDescription($queryStringArr, $realURI, $indexDescription)
    {
        foreach ($queryStringArr as $queryStr) {

            $queryStrArr = explode("=", $queryStr);

            if ($queryStrArr[1] != "") {

                if ($indexDescription != '')
                    $indexDescription .= '|';

                if (Str::contains($queryStr, 'search'))
                    $indexDescription .= 'Search String: ' . $queryStrArr[1];
                else if (Str::contains($queryStr, 'filter')) {

                    $modelName = "\App\Models\\" . Str::singular(Str::before($realURI, '-'));

                    if (class_exists($modelName)) {

                        $modelObj = new $modelName();

                        $filterJsonArr = json_decode(urldecode(base64_decode($queryStrArr[1])), true);

                        if (!is_null($modelObj->activity_log)) {

                            $filterJsonArrGen = [];
                            foreach ($modelObj->activity_log as $log) {

                                $logArr = explode("->", $log);
                                if (count($logArr) == 2) {
                                    $relation = $logArr[0];
                                    $fieldName = $logArr[1];

                                    if (!is_null($filterJsonArr)) {

                                        foreach ($filterJsonArr as $key => $value) {

                                            if ($relation == Str::before($key, '_id')) {

                                                $relationModel = "\App\Models\\" . Str::singular(Str::ucfirst($relation));

                                                $relationValues = $relationModel::whereIn('id', $value)->pluck($fieldName)->toArray();

                                                $filterJsonArrGen[$relation] = $relationValues;
                                            }
                                        }
                                    }
                                }
                            }

                            $indexDescription .= 'Filter String: ' . json_encode($filterJsonArrGen);
                        }
                    }
                } else if (Str::contains($queryStr, 'sort'))
                    $indexDescription .= 'Sort field: ' . Str::before($queryStrArr[1], '_id');
                else if (Str::contains($queryStr, 'order_by')) {
                    if (Str::contains($indexDescription, 'Sort field:'))
                        $indexDescription .= 'Order by: ' . $queryStrArr[1];
                } else if (Str::contains($queryStr, 'page')) {

                    if (Str::contains($indexDescription, 'Page:'))
                        $indexDescription .= 'Per page: ' . $queryStrArr[1];
                    else
                        $indexDescription .= 'Page: ' . $queryStrArr[1];
                }
            }
        }

        return $indexDescription;
    }
}
