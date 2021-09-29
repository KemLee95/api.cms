<?php
namespace App\Http\Controllers\auth;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Role;

use App\Http\Controllers\CommonHelpers;

class UserApi extends ApiBase {

  public function login(Request $req) {
    \Log::info("UserApi: login");
    try {
      $input = $req->all();
      $input['remember_me'] = isset($input['remember_me']) ? true:false;
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
      $credentials = $req->validate([
        'user_name' => 'required',
        'password' => 'required',
      ]);

      $login = Auth::attempt($credentials, $input['remember_me']);
      if($login){
        
        $user = Auth::user();
        $user->token = $user->createToken('CMS_LOGIN_API_TOKEN')->accessToken;
        $user->logout = now()->addYear(1);
        return response()->json(
          [
            'success' => true,
            'user'=> $user
          ], 200);
      }
      else{ 
          return response()->json([
            'success'=> false,
            'message_title'=> 'Unauthorised',
            'message'=>'The user name or password is incorrect!',
            'message_code'=> 401
        ], 401); 
      }  

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

      $token = $user->createToken('TutsForWeb')->accessToken;

      $success['name'] =  $user->name;
      $success['user_name'] = $user->user_name;
      $success['token'] = $token;

      return response()->json([
        "success" => true,
        'user' => $success,
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

  public function logout(Request $req) {
        \Log::info("UserApi: logout");
    try {
      Auth::logout();
      return response()->json([
        "success"=> true,
      ], 200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't logout", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getUser(Request $req) {
    \Log::info("UserApi: login");
    try {
      $input = $req->all();
      $user = User::find(1)->with('roles')->get();
      $roles = Role::with('users','permissions')->get();
      $usr = User::find(2)->isSuperAdmin();
      return response()->json([
        "user" => $usr,
        "role"=>$roles,
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