<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public static function GetMessage($resource, $message){

        return response()->json([
            'message' => $message,
            'data' => $resource,
        ]);

    }

    public static function GetError($message){
        return response()->json(['message' => $message,'errors' => (object)[]], config('constants.validation_codes.unassigned'));
    }
}
