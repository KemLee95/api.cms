<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\ApiBase;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


class AdminApi extends ApiBase {

  public function getRoleList(Request $req){
    \Log::info("AdminApi: get the list of the user roles");
    try {

      $roles = Role::select("id", "name")->get();
      return response()->json([
        "success" => true,
        "roles" => $roles,
      ], 200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't get the role list", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
  
  public function register(Request $req) {
    \Log::info("AdminApi: register the new user");
    try {
      $validator = Validator::make($req->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'user_name' => 'required',
        'password' => 'required',
        're_password' => 'required|same:password',
        'role_id' => 'required|numeric'
      ]);
    
      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }
      $input = $req->all();
      $input['password'] = bcrypt($input['password']);
      unset($input['role_id']);

      $user = User::create($input);
      $token = $user->createToken('CMS_REGISTER_API_TOKEN', ['admin'])->accessToken;
      $success['name'] =  $user->name;
      $success['user_name'] = $user->user_name;
      $success['token'] = $token;

      if($user) {
        $roleUser = RoleUser::create($req->role_id, $user->id);
        if($roleUser->role_id === 1) {
          $success['url'] = env('APP_URL'). 'admin';
        }
        $success['url'] = env('APP_URL') . 'home';
      }

      return response()->json([
        "success" => true,
        "user" => $success,
      ], 200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't resgiter the new user", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getPostList(Request $req) {
    \Log::info("AdminApi: get the post list");
    try {
      $input = $req->all();

      $data = Post::getPostList($req);
      $categories = Category::where("deleted_at", null)->select("id", "name")->get();

      return response()->json([
        "success"=> true,
        "data" =>$data,
        "categories" => $categories
      ], 200);

    } catch (\Exception $e) {
        \Log::error("AdminApi: can't get the post list", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}