<?php

namespace App\Traits;

use App\Http\Resources\DataTrueResource;
use App\Http\Resources\ImportCsvLogsResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function is_array;
use function is_null;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ImportCsvLog;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait Scopes
{

    /** Below method is used for perform sorting for any field from any tables in laravel.
     *
     * @param $query - Query get from controller
     * @param $this - model object variable ( e.g. $this->user got from [ $user = new User() ] )
     * @param $tablename - actual table name (e.g. users)
     * @param $sort - requested $sort value (e.g. first_name or mobile_number)
     * @param $order_by - requested $order_by value (e.g. asc or desc)
     * @param null $groupBy - requested $groupBy value
     *
     * @return mixed
     */
    public function scopeWithOrderBy($query, $sort, $order_by, $tablename, $export_select, $groupBy = null)
    {
        if (is_null($tablename)) // if $tablename is null
            $tablename = $this->getTable(); // get tablename from model
        if ((is_null($sort) || is_null($order_by)) && is_null($groupBy)) { // if sort & order_by is null
            $sort = $tablename . '.' . config('constants.default_sort_field');
            $order_by = 'desc';
        }
        if (!is_null($this->sortable) && in_array($sort, $this->sortable)) { // if sortable is not null property for respected model is exists requested sort field.
            return $query->orderBy($sort, $order_by); // defined order by clause.
        }

        $foreignSortable = $this->foreign_sortable;
        $isTrue = -1;

        if (!is_null($foreignSortable)) {
            foreach ($foreignSortable as $key => $foreignKey) {
                if (is_array($foreignKey)) {
                    $lastForeignKey = $foreignKey[count($foreignKey) - 1];
                    if ($sort == $lastForeignKey)
                        $isTrue = $key;
                } else {
                    if ($sort == $foreignKey)
                        $isTrue = $key;
                }
            }
        }

        //if foreign_sortable is not null property for respected model is exists requested sort field & foreign_table & foreign_key is not null
        if (!is_null($this->foreign_sortable) && !is_null($this->foreign_table) && !is_null($this->foreign_key)) {
            for ($i = 0; $i < count($this->foreign_sortable); $i++) {
                if ($isTrue == $i) {
                    if (!is_null($this->pivot_table))
                        $query->join($this->foreign_table[$i], $this->pivot_table[$i] . '.' . $this->foreign_sortable[$i], '=', $this->foreign_table[$i] . '.id');
                    else {
                        if (is_array($this->foreign_sortable[$i]) && is_array($this->foreign_table[$i])) {
                            $foreignSortables = $this->foreign_sortable[$i];
                            $foreignTables = $this->foreign_table[$i];
                            for ($j = 0; $j < count($foreignSortables); $j++) {
                                if ($j == 0)
                                    $query->join($foreignTables[$j], $tablename . '.' . $foreignSortables[$j], '=', $foreignTables[$j] . '.id');
                                else
                                    $query->join($foreignTables[$j], $foreignTables[$j - 1] . '.' . $foreignSortables[$j], '=', $foreignTables[$j] . '.id');
                            }
                        } else
                            $query->join($this->foreign_table[$i], $tablename . '.' . $this->foreign_sortable[$i], '=', $this->foreign_table[$i] . '.id');
                    }
                    if (!$export_select) {
                        $query->select($tablename . '.*');
                    }
                    if (is_array($this->foreign_key[$i])) {
                        $foreignKeys = $this->foreign_key[$i];
                        $foreignTables = $this->foreign_table[$i];
                        for ($j = 0; $j < count($foreignKeys); $j++) {
                            if (is_array($foreignTables))
                                $query->orderBy($foreignTables[count($foreignTables) - 1] . '.' . $foreignKeys[$j], $order_by); // defined foreign table order by clause.
                            else
                                $query->orderBy($foreignTables . '.' . $foreignKeys[$j], $order_by); // defined foreign table order by clause.
                        }
                    } else
                        $query->orderBy($this->foreign_table[$i] . '.' . $this->foreign_key[$i], $order_by); // defined foreign table order by clause.
                }
            }
            if ($isTrue != -1)
                return $query;
        }

        $typeSortable = $this->type_sortable;
        $isTrue = -1;

        if (!is_null($typeSortable)) {
            foreach ($typeSortable as $key => $typeKey) {
                if (is_array($typeKey)) {
                    $lastTypeKey = $typeKey[count($typeKey) - 1];
                    if ($sort == $lastTypeKey)
                        $isTrue = $key;
                } else {
                    if ($sort == $typeKey)
                        $isTrue = $key;
                }
            }
        }


        //if type_sortable is not null property for respected model is exists requested sort field & type_enum & type_enum_text is not null
        if (!is_null($this->type_sortable) && !is_null($this->type_enum) && !is_null($this->type_enum_text)) {
            for ($j = 0; $j < count($this->type_sortable); $j++) { // for loop type_sortable property array.
                if ($isTrue == $j) {
                    $logic = '';
                    $typeSortable = $this->type_sortable[$j];
                    for ($k = 0; $k < count($this->type_enum[$j]); $k++) { // for loop type_enum array of array
                        if (is_array($typeSortable))
                            $sort = $typeSortable[count($typeSortable) - 1];
                        $logic .= 'WHEN ' . $sort . ' = "' . config($this->type_enum[$j][$k]) . '" THEN "' . config($this->type_enum_text[$j][$k]) . '"'; // generate DB:raw query
                    }
                    $textValue = 'laravel';
                    if (!$export_select) {
                        $query->select($tablename . '.*', DB::raw('(CASE ' . $logic . ' ELSE "" END) AS ' . $textValue . '_text'));
                    } else {
                        $query->addSelect(DB::raw('(CASE ' . $logic . ' ELSE "" END) AS ' . $textValue . '_text'));
                    }
                    if (is_array($typeSortable)) {
                        for ($k = 0; $k < count($typeSortable); $k += 4) {
                            if ($k == 0)
                                $query->join($typeSortable[$k], $tablename . '.' . $typeSortable[$k + 1], '=', $typeSortable[$k] . '.id');
                            else
                                $query->join($typeSortable[$k], $typeSortable[$k - 4] . '.' . $typeSortable[$k + 1], '=', $typeSortable[$k] . '.id');
                        }
                    }
                    $query->orderBy($textValue . '_text', $order_by); // perform order by
                }
            }
        }
        if ($isTrue != -1)
            return $query;
    }

    /** Below method is used for perform searching for any field from any tables in laravel.
     *
     * @param $query - Query get from controller
     * @param $this - model object variable ( e.g. $this->user got from [ $user = new User() ] )
     * @param $search - search keyword
     *
     * @return mixed
     */
    public function scopeWithSearch($query, $search, $export_select = false)
    {
        if (is_null($search)) { // if $search is null
            return $query;
        }

        $searches = $this->sortable; // Get model $sortable property - When defined on which field search is apply.
        if (!is_null($searches)) {
            $query->where(function ($query) use ($search, $searches) {
                //defined multiple fields search is apply.
                foreach ($searches as $find) {
                    $query->orWhere($find, 'LIKE', "%$search%");
                }
            });
        }
        $foreign_keys = $this->foreign_key; // Get model $foreign_key property - It is foreign table field name which searching is applied on it. (e.g. User model roles table role field apply on search).
        $foreign_methods = $this->foreign_method; // Get model $foreign_method property - It is model method which is defined for relationship between model. (e.g. user model role() method).
        if (!is_null($foreign_keys) && !is_null($foreign_methods)) {
            $where = 'where';
            if (!is_null($searches))
                $where = 'orWhere';
            $query->$where(function ($query) use ($search, $foreign_keys, $foreign_methods) {
                for ($i = 0; $i < count($foreign_keys); $i++) {
                    //defined multiple foreign key fields search is apply.
                    $query->orWhereHas($foreign_methods[$i], function ($query) use ($search, $foreign_keys, $i) {
                        if (is_array($foreign_keys[$i]))
                            $query->where(DB::raw('CONCAT(' . implode('," ",', $foreign_keys[$i]) . ')'), 'LIKE', "%$search%");
                        else
                            $query->where($foreign_keys[$i], 'LIKE', "%$search%");
                    });
                }
            });
        }

        $combineds = $this->combined; // Get model $combined property - It is array of fields which is concate and apply search on it. ( e.g. User model title+first_name+last_name (Mr Chirag Parmar)). Search perform on this combined string.
        if (!is_null($combineds)) {
            $where = 'where';
            if (!is_null($searches) && !is_null($foreign_keys) && !is_null($foreign_methods))
                if (!is_null($searches) || (!is_null($foreign_keys) && !is_null($foreign_methods)))
                    $where = 'orWhere';
            $query->$where(function ($query) use ($search, $combineds) {
                foreach ($combineds as $combined) {
                    //defined search on combined field.
                    $query->orWhere(DB::raw('CONCAT(' . implode('," ",', $combined) . ')'), 'LIKE', "%$search%");
                }
            });
        }

        $type_sortables = $this->type_sortable; // Get model $type_sortable property - It is enum type field. (e.g. M - Male, F - Female).
        $type_enums = $this->type_enum; // Get model $type_enum property - (parse enum value e.g. '0','1' ... and so on).
        $type_enum_texts = $this->type_enum_text; // Get model $type_enum_text - (parse enum text value e.g. 'Male', 'Female' ... and so on).

        //if (!is_null($type_sortables) && !is_null($type_enums) && !is_null($type_enum_texts) && !$export_select) {
        if (!is_null($type_sortables) && !is_null($type_enums) && !is_null($type_enum_texts)) {
            for ($j = 0; $j < count($type_sortables); $j++) {
                $logic = '';
                for ($k = 0; $k < count($type_enums[$j]); $k++) {
                    if (is_array($type_sortables[$j]))
                        $logic .= 'WHEN ' . $type_sortables[$j][count($type_sortables[$j]) - 1] . ' = "' . config($type_enums[$j][$k]) . '" THEN "' . config($type_enum_texts[$j][$k]) . '"';
                    else
                        $logic .= 'WHEN ' . $type_sortables[$j] . ' = "' . config($type_enums[$j][$k]) . '" THEN "' . config($type_enum_texts[$j][$k]) . '"'; //defined case when condition for enum type and it's text.
                }
                $type_table = $this->type_table;
                if (!is_null($type_table)) {
                    $query = $query->select($type_table . '.*', DB::raw('(CASE ' . $logic . ' ELSE "" END)'))
                        ->orWhere(DB::raw('(CASE ' . $logic . ' ELSE "" END)'), 'LIKE', "%$search%");
                } else {
                    if (!$export_select)
                        $query = $query->select('*', DB::raw('(CASE ' . $logic . ' ELSE "" END)'))
                            ->orWhere(DB::raw('(CASE ' . $logic . ' ELSE "" END)'), 'LIKE', "%$search%");
                    else
                        $query = $query->orWhere(DB::raw('(CASE ' . $logic . ' ELSE "" END)'), 'LIKE', "%$search%");
                }
                if (is_array($type_sortables[$j])) {
                    $tablename = $this->getTable();
                    $typeSortable = $type_sortables[$j];
                    for ($k = 0; $k < count($typeSortable); $k += 4) {
                        if ($k == 0)
                            $query->join($typeSortable[$k], $tablename . '.' . $typeSortable[$k + 1], '=', $typeSortable[$k] . '.id');
                        else
                            $query->join($typeSortable[$k], $typeSortable[$k - 4] . '.' . $typeSortable[$k + 1], '=', $typeSortable[$k] . '.id');
                    }
                }
                $query = $query->orWhere(DB::raw('(CASE ' . $logic . ' ELSE "" END)'), 'LIKE', "%$search%");
            }
        }
        return $query;
    }

    /**
     * @param $query - Query get from controller
     * @param $no_pages - parse number of pages
     * @return mixed
     */
    public function scopeWithPerPage($query, $no_pages)
    {
        if (is_null($no_pages)) {
            $no_pages = config('constants.paginate');
        }
        return $query->paginate($no_pages);
    }

    public function scopeCodeGenerator($query, $id, $length, $prefix = "")
    {
        $loop_length = ($length - strlen($id));
        $code_zeros = $prefix;
        for ($i = 0; $i < $loop_length; $i++)
            $code_zeros .= '0';
        return ($code_zeros . $id);
    }

    /**
     * This function will perform filter function.
     * @param $query - Query get from controller
     * @param $filters - filter records
     * @return mixed
     */
    public function scopeWithFilter($query, $filters, $request = null)
    {
        // get filters from request in JSON format.
        //$filters = json_decode(urldecode(base64_decode($filters))); // IF YOU USE URL-ENCODING Uncomment this line and remove below line.
        $filters = json_decode($filters); // IF YOU USE URL-ENCODING Uncomment this line and remove below line.

        // Apply filter if it is not null
        if (!is_null($filters)) {
            $query->where(function ($query) use ($filters, $request) {
                // Apply filter for each values
                foreach ($filters as $filterColumn => $filterValue) {

                    if ($filterColumn == "cf") {
                        $query->where(function ($query) use ($filterValue) {

                            if ($filterValue !== "") {

                                foreach ($filterValue as $key => $value) {

                                    foreach ($value as $val) {

                                        foreach ($val as $k => $v) {
                                            $query->where($key, $k, $v);
                                        }
                                    }
                                }
                            }
                        });
                    } else if ($filterColumn == "pf") {  //Apply pivot column filter

                        foreach ($filterValue as $key => $value) {

                            $query->whereHas($key, function ($query) use ($value) {
                                $query->whereIn('id', $value);
                            });
                        }
                    } else if ($filterColumn == "ff") {  //Apply foreign table column filter

                        if (is_array($filterValue)) {

                            foreach ($filterValue as $value) {

                                foreach ($value as $key => $val) {

                                    $query->whereHas($key, function ($query) use ($val) {

                                        foreach ($val as $k => $v) {

                                            $query->whereIn($k, $v);
                                        }
                                    });
                                }
                            }
                        }
                    } else if ($filterColumn == "off") {  //Apply operator foreign table column filter

                        if (is_array($filterValue)) {

                            foreach ($filterValue as $value) {

                                foreach ($value as $key => $val) {

                                    $query->whereHas($key, function ($query) use ($val) {

                                        foreach ($val as $k => $v) {

                                            foreach ($v as $innerValue) {

                                                foreach ($innerValue as $inKey => $inVal) {
                                                    $query->where($k, $inKey, $inVal);
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    } else if (!is_array($filterValue)) {  //check whether value is date range value
                        $dates = explode("to", $filterValue); //get two dates

                        if (!is_numeric(trim($dates[0]))) {

                            if (Carbon::createFromFormat('Y-m-d', trim($dates[0])) !== false) {
                                $startDate = $this->getEpochfromDateTime(trim($dates[0]) . ' 00:00:00', config('constants.server_timezone'));
                                $dates[0] = $this->getDatefromEpoch($startDate, config('constants.server_timezone'), 'Y-m-d H:i:s');
                            }
                        }

                        if (!is_numeric(trim($dates[1]))) {

                            if (Carbon::createFromFormat('Y-m-d', trim($dates[1])) !== false) {
                                $endDate = $this->getEpochfromDateTime(trim($dates[1]) . ' 23:59:59', config('constants.server_timezone'));
                                $dates[1] = $this->getDatefromEpoch($endDate, config('constants.server_timezone'), 'Y-m-d H:i:s');
                            }
                        }

                        $query->whereBetween($filterColumn, $dates); //filter values based on dates.
                    } else if ($filterValue !== '') {
                        $query->whereIn($filterColumn, $filterValue);
                    }
                }
            });
            //dd(\Str::replaceArray('?', $query->getBindings(), $query->toSql()));
            return $query;
        }
        return $query;
    }
    /**
     * This function will check whether date range is valid or not.
     * The format for date range is YYYY-MM-DDtoYYYY-MM-DD.
     * @param $date
     * @return boolean
     */
    function isDateRange($date)
    {
        if (!is_object($date) && preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])to[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date))
            return true;
        else
            return false;
    }

    /**
     * Export Common for the Type enums.
     *
     * @param $query
     * @return \Illuminate\Database\Query\Expression
     */
    public function scopeWithTypeEnum($query, $sort = '')
    {
        $logic = '';
        if (!is_null($this->type_enum) && !is_null($this->type_enum_text)) {
            if ($sort == '') {
                $sort = $this->type_sortable[0];
                $field_name = $this->type_sortable[0];
            } else {
                $field_name = $sort;
            }
            if ($sort == 'attributes_subtext') {
                $field_name = 'attribute';
            }
            $j = array_search($sort, $this->type_sortable);
            for ($k = 0; $k < count($this->type_enum[$j]); $k++) { // for loop type_enum array of array
                $logic .= 'WHEN ' . $field_name . ' = "' . config($this->type_enum[$j][$k]) . '" THEN "' . config($this->type_enum_text[$j][$k]) . '"'; // generate DB:raw query
            }
        }
        $type_text = DB::raw('(CASE ' . $logic . ' ELSE "" END) AS ' . $sort . '_text');
        return $type_text;
    }

    /**
     * Import csv
     *
     * @param $query
     * @param $request
     * @param $model
     * @param $modelType
     * @param $folderName
     * @return \Illuminate\Http\JsonResponse
     */
    public function scopeImportBulk($query, $request, $model, $modelType, $folderName)
    {
        if ($request->hasfile('file')) {

            $only_file_name = str_replace(
                ' ',
                '_',
                strtolower(pathinfo(
                    $request->file('file')->getClientOriginalName(),
                    PATHINFO_FILENAME
                ))
            );
            $only_extension = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_EXTENSION);

            $filename = $only_file_name . '_' . config('constants.file.name') . '.' . $only_extension;

            $path = $request->file('file')->storeAs('/' . $folderName, $filename, 'public');
            $file_path = $folderName . pathinfo($path, PATHINFO_BASENAME);
            Excel::import($model, $path, 'public');
            if (count($model->getErrors()) > 0) {
                $error_json = json_encode($model->getErrors());
                $importCsvLog = ImportCsvLog::create([
                    'file_path' => $file_path,
                    'filename' => $filename,
                    'model_name' => $modelType,
                    'user_id' => 38,
                    'status' => config('constants.import_csv_log.status_enum.0'),
                    'error_log' => $error_json
                ]);
                return response()->json(['message' => config('constants.messages.import_fail'), 'errors' => $model->getErrors(), 'data' => new ImportCsvLogsResource($importCsvLog)], config('constants.validation_codes.unassigned'));
            }

            $importCsvLog = ImportCsvLog::create([
                'file_path' => $file_path,
                'filename' => $filename,
                'model_name' => $modelType,
                'user_id' => 38,
                'status' => config('constants.import_csv_log.status_enum.1'),
                'no_of_rows' => $model->getRowCount()
            ]);

            return response()->json(['message' => config('constants.messages.import_success'), 'data' => new ImportCsvLogsResource($importCsvLog)]);
        } else {
            return User::GetError(config('constants.messages.file_csv_error'));
        }
    }

    /**
     * This common method is used to delete model restriction will be checked it's exist somewhere or not
     *
     * @param $query
     * @param $models
     * @param $commonIdKey
     * @param $commonIdValue
     * @return array
     */
    public function scopeCommonCodeForDeleteModelRestrictions($query, $models, $commonIdKey, $commonIdValue)
    {
        $inUse = [];

        foreach ($models as $model) {
            $res = $this->commonCodeForCheckModelIsUseInOtherPlace($model, $commonIdKey, $commonIdValue);
            if ($res != "")
                $inUse[] = $res;
        }

        return $inUse;
    }

    /**
     * This is sub-common method for delete restriction for inner level checking code
     *
     * @param $model
     * @param $id
     * @param $value
     * @return string
     */
    public function commonCodeForCheckModelIsUseInOtherPlace($model, $id, $value)
    {
        $response = $model::whereIn($id, $value)->first();
        if ($response) {
            $model_name = new $model;
            return ucfirst(str_replace('_', ' ', $model_name->getTable()));
        }

        return "";
    }

    /**
     *  Get date from timestamp
     *
     * @param $timestamp
     * @return string
     */
    public function scopeGetDateFromTimestamp($query, $timestamp)
    {
        return \Carbon\Carbon::parse(\Carbon\Carbon::createFromTimestamp($timestamp, 'UTC')
            ->toDateTimeString())->format('Y-m-d H:i:s');
    }

    /**
     *  Common manual pagination method
     *
     * @param $query
     * @param $items
     * @param $perPage
     * @param int $page
     * @param array $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function scopePaginateCollection($query, $items, $perPage, $page = 1, $options = [])
    {
        $perPage = is_null($perPage) ? 10 : $perPage;
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);
        return new \Illuminate\Pagination\LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
        //ref for array_values() fix: https://stackoverflow.com/a/38712699/3553367
    }

    /**
     * Check specific model any field value already exist or not (Both times Create as well as Update method)
     *
     * @param $query - Calling object
     * @param $request - Request object
     * @param $fieldName - If you specify specific field is check
     * @return bool - Return true or false boolean value
     *
     */
    public function scopeFieldExist($query, $request, $fieldName)
    {
        $response = $query->where($fieldName, $request->get($fieldName))->first();

        if ($response == null) // Check $response variable is NULL
            return true;
        else {
            if (!is_null($request->get('id'))) { // Check request id variable value is NOT NULL
                if ($response->id == $request->get('id')) // Check $response object id field and request id variable is same
                    return true;
            }
        }

        return false;
    }

    /**
     * UploadZipFile
     * @param $query
     * @param $request
     * @return DataTrueResource|\Illuminate\Http\JsonResponse
     */
    public function scopeUploadZipFile($query, $request)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($request->file('file'));
        if ($res === TRUE) {
            $zip->extractTo(storage_path('app/public/'));
            $zip->close();
            return new DataTrueResource(true, config('constants.messages.upload_zip_success'));
        } else {
            return response()->json(['error' => 'failed'], config('constants.validation_codes.unassigned'));
        }
    }

    /**
     * Common PDF Header
     *
     * @param $query
     * @param $request
     * @param $code
     * @param string $pdfName
     * @return string
     */
    public function scopeApplyCommonPdfHeader($query, $request, $pdfName = 'Default Name')
    {
        $html = '<div class="full-width">
                    <table style="width:100%">
                        <tr align="bottom">
                            <td style="width:30%;">
                                <div style="text-align: left">
                                   <img src="' . public_path('/admin-images/logo.png') . '" alt="Logo" width="150"/>';

        $html .= '</div>
                            </td>
                            <td align="right">
                                <div style="text-align:right;height:auto;min-height:1px;">
                                    <h2 style="margin-top: 0;">' . strtoupper($pdfName) . '</h2>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                ';
        return $html;
    }

    /**
     * Common PDF Footer
     *
     * @param $query
     * @param $request
     * @return string
     */
    public function scopeApplyCommonPdfFooter($query, $request)
    {
        return '<div class="full-width" align="center" style="position: fixed;left: 0;bottom: 3%;color: #666;font-size: 12px;">
                    <hr style="color:#000;">
                    AIC Team<br>                   
                    T. +61 (0) 3 9545 3333 E. contact@autoic.com.au
                </div>';
    }

    /**
     *  Get Date from Epoch
     *
     * @param $query
     * @param $epochtime
     * @param string $timezone
     * @param string $format
     * @return string
     */
    public function scopeGetDatefromEpoch($query, $epochtime, $timezone = 'Australia/Perth', $format = 'Y-m-d H:i:s')
    {

        $dateTime = Carbon::createFromTimestamp($epochtime, $timezone)
            ->toDateTimeString(); //remove this one if u want to return Carbon object

        return Carbon::parse($dateTime)->format($format);
    }

    /**
     *  Get Epoch from DateTime
     *
     * @param $query
     * @param $date
     * @param string $timezone
     * @return mixed
     */
    public function scopeGetEpochfromDateTime($query, $date, $timezone = 'Australia/Perth', $format = 'Y-m-d H:i:s')
    {

        return Carbon::createFromFormat($format, $date, $timezone)
            ->timestamp; //remove this one if u want to return Carbon object

    }

    /**
     * Get Business days between timestamps
     */
    public function scopeGetBusinessDaysBetweenTimestamps($query, $startTimestamp, $endTimestamp)
    {
        $start = Carbon::parse($this->getDatefromEpoch($startTimestamp, config('constants.server_timezone'), 'Y-m-d'));
        $end = Carbon::parse($this->getDatefromEpoch($endTimestamp, config('constants.server_timezone'), 'Y-m-d'));

        return $start->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekday();
        }, $end);
    }

    /**
     * Add Business Days in Date
     */
    public function scopeAddBusinessDaysInTimestamp($query, $startDate)
    {
        $startDate = Carbon::parse(User::getDatefromEpoch($startDate, config('constants.server_timezone'), 'Y-m-d'));

        $i = 1;
        do {
            if (!$startDate->isWeekend())
                $i++;

            $startDate->addDay();
        } while ($i <= config('constants.subscription_module.manual_upgrading_business_days'));

        return $startDate;
    }

    /**
     *  Get Exclusive GST amount
     *
     * @param $query
     * @param $total
     * @return float|int
     */
    public function scopeGetExclusiveGSTAmt($query, $total)
    {
        return round(($total * 100) / (100 + config('constants.gst_percentage')), 2);
    }

    /**
     *  Get Inclusive GST
     *
     * @param $query
     * @param $total
     * @return float|int
     */
    public function scopeGetInclusiveGST($query, $total)
    {
        return round(($total * config('constants.gst_percentage')) / (100 + config('constants.gst_percentage')), 2);
    }

    /**
     *  Get Exclusive GST
     *
     * @param $query
     * @param $total
     * @return float|int
     */
    public function scopeGetGST($query, $total)
    {
        return round($total * config('constants.gst_percentage') / 100, 2);
    }

    /**
     * @param $query
     * @param $model
     * @param $folder
     * Delete single with thumbnail
     */
    public function scopeSingleImageDelete($query, $model, $folder)
    {
        $model->deleteOne($folder . $model->id . '/' . basename($model->image)); // Delete main image
        $model->deleteOne($folder . $model->id . '/thumbs/' . basename($model->image_thumbnail)); // Delete thumbnail image
        $folderPath = public_path('storage/' . $folder . $model->id);
        if (File::isDirectory($folderPath)) {
            File::deleteDirectory($folderPath);
        }
    }

    /**
     *  Add Created By & Update By Id for Guest User
     *
     * @param $model
     * @param $guestUserId
     */
    public static function addCreatedByUpdateByForGuestUser($model, $guestUserId)
    {
        $model->created_by = $guestUserId;
        $model->updated_by = $guestUserId;
        $model->save();
    }

    /**
     * Get Random string
     */
    public static function getRandomString($length = 10)
    {
        return Str::random($length);
    }

}
