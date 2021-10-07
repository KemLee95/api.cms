<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller as ControllerBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;


class AuthController extends ControllerBase {

  public function login(Request $req) {

    \Log::info("AuthController: login");
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
          $success->url = env('CLIENT_URL') .'admin';
        } else {
          $success->token = $user->createToken('CMS_LOGIN_API_TOKEN', ['user'])->accessToken;
          $success->url = env('CLIENT_URL') .'home';
          $success->isAdmin = false;
        }

        UserLogin::saveUserLogin($user->id);

        return response()->json([
            'success' => true,
            'user'=> $success,
          ], 200);
      }

      return response()->json([
          "success" => false,
          'message_title'=> 'Unauthorised',
          'message'=>'The user name or password is incorrect!',
          'message_code'=> 401
      ], 401); 

    } catch (\Exception $e) {
        \Log::error("AuthController: can't login!", ['eror message' => $e->getMessage()]);
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

    \Log::info("AuthController: register");
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

          $roleIdList = $req->has('role_id');

          if(!$req->has("role_id")) {
            return response()->json([
              "success" => false,
              "message" => "the role_id is required!",
              "message_title" => "Request failed"
            ], 400);
          }
          $roleIdList = json_decode($req->role_id);
          for($idx = 0; $idx < count($roleIdList); $idx++){
            RoleUser::saveRoleUser($roleIdList[$idx], $user->id);
          }

        } else {
          $roleUser = RoleUser::saveRoleUser(2, $user->id);
        }
        UserStatus::saveUserStatus($user->id, UserStatus::ENABLED_STATE);
      }
      //
      event(new Registered($user));

      return response()->json([
        "success" => true,
        "message_title" => "Successful!",
        'message' => "User registation successful! Please check your email to verify!",
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

  public function logout() {
    \Log::info("AuthController: logout");
    try {

      Auth::user()->token()->delete();
      return response() -> json([
        "success" => true,
        "message" => "Logged Out Successfully!",
        "message_title" => "Successful"
      ], 200);
      
    } catch (\Exception $e) {
        \Log::error("UserApi: can't logout!", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400);
    }
  }
}