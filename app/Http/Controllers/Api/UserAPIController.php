<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\DataTrueResource;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CsvRequest;
use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserGallery;
use App\Models\UserPicture;
use App\Models\UserHobby;
class UserAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->get('is_light',false)){
            return Cache::rememberForever('user.all', function () use($request){
                $user = new User();
                $query = User::commonFunctionMethod(User::select($user->light),$request,true);
                return new UserCollection(UserResource::collection($query),UserResource::class);
            });
        }
        else{
            $query = User::commonFunctionMethod(User::with(['role','user_galleries']),$request,true);
            return new UserCollection(UserResource::collection($query),UserResource::class);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        if ($request->hasFile('profile')) {
            $realPath = 'user/' . $user->id;
            $resizeImages = $user->resizeImages($request->file('profile'), $realPath, 100, 100);
            $user->update([
                'profile' => $resizeImages['image'],
                'profile_original' => $resizeImages['original'],
                'profile_thumbnail' => $resizeImages['thumbnail']
            ]);
        }
        if ($request->get('hobby_id')) {
            foreach ($request->get('hobby_id') as $hobby) {
                UserHobby::create([
                    'user_id' => $user->id,
                    'hobby_id' => $hobby,
                ]);
            }
        }
        if ($request->hasFile('user_galleries')) {
            $realPath = 'user/' . $user->id . '/user_galleries'; 
            foreach ($request->file('user_galleries') as $vImgs) {
                $resizeImages = $user->resizeImages($vImgs, $realPath, 100, 100);
                UserGallery::create([
                    'user_id' => $user->id,
                    'gallery' => $resizeImages['image'],
                    'gallery_original' => $resizeImages['original'],
                    'gallery_thumbnail' => $resizeImages['thumbnail']
                ]);
            }
         }
         if ($request->hasFile('user_pictures')) {
            $realPath = 'user/' . $user->id . '/user_pictures';
 
            foreach ($request->file('user_pictures') as $vImgs) {
                $resizeImages = $user->resizeImages($vImgs, $realPath, 100, 100);
                UserPicture::create([
                    'user_id' => $user->id,
                    'picture' => $resizeImages['image'],
                    'picture_original' => $resizeImages['original'],
                    'picture_thumbnail' => $resizeImages['thumbnail']
                ]);
            }
         }
        return User::GetMessage(new UserResource($user), config('constants.messages.create_success'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
    	if (is_null($user)) {
            return User::GetError(config('constants.messages.not_found'));
        }
        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(UserUpdateRequest $request,$id)
    {
        $data = $request->all();
        $user = User::findOrFail($id);
        if ($request->get('hobby_id')) {
            UserHobby::where(['user_id' => $user->id])->delete();

            foreach ($request->get('hobby_id') as $hobby) {
                UserHobby::create([
                    'user_id' => $user->id,
                    'hobby_id' => $hobby,
                ]);
            }
        }
        if ($request->hasFile('profile')) {
            $realPath = 'user/' . $user->id;

            \Illuminate\Support\Facades\Storage::deleteDirectory('/public/' . $realPath);

            $resizeImages = $user->resizeImages($request->file('profile'), $realPath, 100, 100);

            $data['profile'] = $resizeImages['image'];
            $data['profile_original'] = $resizeImages['original'];
            $data['profile_thumbnail'] = $resizeImages['thumbnail'];
        }
        if ($request->hasFile('user_galleries')) {
            \App\Models\UserGallery::where(['user_id' => $user->id])->delete();

            $realPath = 'user/' . $user->id . '/user_galleries';
            \Illuminate\Support\Facades\Storage::deleteDirectory('/public/' . $realPath);

            foreach ($request->file('user_galleries') as $v_imgs) {
                $resizeImages = $user->resizeImages($v_imgs, $realPath, 100, 100);
                \App\Models\UserGallery::create([
                   'user_id' => $user->id,
                   'gallery' => $resizeImages['image'],
                   'gallery_original' => $resizeImages['original'],
                   'gallery_thumbnail' => $resizeImages['thumbnail']
                ]);
            }
        }
        if ($request->hasFile('user_pictures')) {
            \App\Models\UserPicture::where(['user_id' => $user->id])->delete();

            $realPath = 'user/' . $user->id . '/user_pictures';
            \Illuminate\Support\Facades\Storage::deleteDirectory('/public/' . $realPath);

            foreach ($request->file('user_pictures') as $v_imgs) {
                $resizeImages = $user->resizeImages($v_imgs, $realPath, 100, 100);
                \App\Models\UserPicture::create([
                   'user_id' => $user->id,
                   'picture' => $resizeImages['image'],
                   'picture_original' => $resizeImages['original'],
                   'picture_thumbnail' => $resizeImages['thumbnail']
                ]);
            }
        }

        $user->update($data);
        return User::GetMessage(new UserResource($user), config('constants.messages.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param User $user
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id == config('constants.system_user_id')){
            return User::GetError(config('constants.messages.admin_user_delete_error'));    
        }   
        
        $user->singleImageDelete($user, "user/"); // Delete image    
            
        $user->delete();
        
        return new DataTrueResource($user,config('constants.messages.delete_success'));
        
        
    }

    public function deleteAll(Request $request,User $user)
    {
        if(!empty($request->id)) {
        $ids = explode(",", $request->id);

            if (in_array(config('constants.system_user_id'), $ids)){
                return User::GetError(config('constants.messages.admin_user_delete_error'));
            }
            User::whereIn('id', $ids)->get()->each(function($user) {                  
                $user->singleImageDelete($user, "user/"); // Delete image  
                        $user->delete();
                });
            return new DataTrueResource(true,config('constants.messages.delete_success'));
        }
        else{
            return User::GetError(config('constants.messages.delete_multiple_error'));
        }
    }

    /**
     * Export User Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportUser(Request $request)
    {
        return Excel::download(new UserExport($request), 'user_' . config('constants.file.name') . '.csv');

    }

    /**
      * Import bulk
      * @param CsvRequest $request
      * @return \Illuminate\Http\JsonResponse
      */
      public function importBulk(CsvRequest $request)
      {
         return User::importBulk($request,new UserImport(),'user','import/user/');
      }
      /**
     * This is a batch request API
     *
     * @param Request $requestObj
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchRequest(Request $requestObj)
    {
        $requests  = $requestObj->get('request');//get request
        $output = array();
        $cnt = 0;
        foreach ($requests as $request) {// foreach for all requests inside batch

            $request = (object) $request;// array request convert to object

            if($cnt == 10)// limit maximum call 10 requests
                break;

            $url = parse_url($request->url);

            //querystrings code
            $query = array();
            if (isset($url['query'])) {
                parse_str($url['query'], $query);
            }

            $server = ['HTTP_HOST'=> preg_replace('#^https?://#', '', URL::to('/')), 'HTTPS' => 'on'];
            $req = Request::create($request->url, 'GET', $query, [],[], $server);// set request

            $req->headers->set('Accept', 'application/json');//set accept header
            $res = app()->handle($req);//call request

            if (isset($request->request_id)) {// check request_id is set or not
                $output[$request->request_id] = json_decode($res->getContent()); // get response and set into output array
            } else {
                $output[] = $res;
            }

            $cnt++;// request counter
        }

        return response()->json(array('response' => $output));// return batch response
    }
}
