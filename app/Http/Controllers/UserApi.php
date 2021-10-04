<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use Illuminate\Auth\Events\Registered;

use App\Http\Controllers\CommonHelpers;

class UserApi extends ApiBase {

  public function login(Request $req) {

    \Log::info("UserApi: login");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'user_name' => 'required',
        'password' => 'required'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $input['remember_me'] = isset($input['remember_me']) ? true : false;
      $credentials = $req->validate([
        'user_name' => 'required',
        'password' => 'required',
      ]);

      $login = Auth::attempt($credentials, $input['remember_me']);
      if($login){
        
        $user = Auth::user();
        $success = User::select("id", "user_name")->find($user->id);
        $success->logout = now()->addYear(1);

        $isAdmin = User::find($user->id)->hasRole('admin');
        if($isAdmin) {
          $success->token = $user->createToken('CMS_LOGIN_API_TOKEN', ['admin'])->accessToken;
          $success->isAdmin = $isAdmin;
          $success->url = env('APP_URL') .'admin';
        } else {
          $success->token = $user->createToken('CMS_LOGIN_API_TOKEN', ['user'])->accessToken;
          $success->url = env('APP_URL') .'home';
          $success->isAdmin = false;
        }

        UserLogin::saveUserLogin($user->id);

        return response()->json([
            'success' => true,
            'user'=> $success,
          ], 200);
      }

      return response()->json([
          'message_title'=> 'Unauthorised',
          'message'=>'The user name or password is incorrect!',
          'message_code'=> 401
      ], 401); 

    } catch (\Exception $e) {
        \Log::error("UserApi: can't login!", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed",
            'message_code' => 401,
        ], 400 );
    }
  }

  public function register(Request $req) {

    \Log::info("UserApi: register");
    try {
      $validator = Validator::make($req->all(), [
          'name' => 'required',
          'email' => 'required|email',
          'user_name' => 'required',
          'password' => 'required',
          're_password' => 'required|same:password'
      ]);

      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $input = $req->all();
      $input['password'] = bcrypt($input['password']);
      $user = User::create($input);

      if($user) {
        if(Auth::user() && Auth::user()->hasRole('admin')) {

          $roleId = $req->has('role_id') ? $req->role_id : 2;
          $roleUser = RoleUser::create($roleId, $user->id);
        } else {
          $roleUser = RoleUser::create(2, $user->id);
        }
      }
      
      event(new Registered($user));
      return response()->json([
        "success" => true,
        "message_title" => "Successful!",
        'message' => "User Registation Successful! Please login",
      ], 200);
      
    } catch (\Exception $e) {
        \Log::error("UserApi: can't register!", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400);
    }
  }

  public function getUserList(Request $req) {
    \Log::info("UserApi: login");
    try {
      $input = $req->all();
      $accounts = User::getUserList($req);

      $user = User::getInactiveUser();

      return response()->json([
        "success" => true,
        "accounts" =>$accounts,
      ], 200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't get the user info", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function checkUniqueUser(Request $req) {
    \Log::info("UserApi: check unique user");
    try {

      $input = $req->all();
      if($req->has('user_name') || $req->has('email')) {

        if($req->has('user_name')) {
          $user = User::where("deleted_at", null)->where("user_name", $input['user_name'])->first();
          if($user) {
            return response()->json([
              "success"=> true,
              "unique" => false
            ],200);
          }
          return response()->json([
            "success"=> true,
            "unique" => true
          ],200);
        }

        if($req->has('email')) {
          $user = User::where("deleted_at", null)->where("email", $input['email'])->first();
          if($user) {
            return response()->json([
              "success"=> true,
              "unique" => false
            ],200);
          }
          return response()->json([
            "success"=> true,
            "unique" => true
          ],200);
        }
      }
      return response()->json([
        "success"=>false,
        "message"=> 'Wrong input data',
        "message_title"=> "The username or email is required",
      ], 400);
    } catch (\Exception $e) {
        \Log::error("UserApi: checking unique user failed", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}