<?php
namespace App\Http\Controllers\auth;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Http\Controllers\CommonHelper;


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
        $user->token = $user->createToken('LOGIN_API_TOKEN')->accessToken;

        $userUpdate = User::find($user->id);
        // $vaToken = CommonHelper::generateRandomString($user->token . CommonHelper::getUniqueId(), 50);
        // $userUpdate->vaToken = $vaToken;
        $userUpdate->save();

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
            'message' => 'Please try again',
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

      $success['token'] =  $user->createToken('APPLICATION')->accessToken; 
      $success['name'] =  $user->name;

      return response()->json([
        "success" => true,
        'value' => $success,
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
      $user = User::find(7);
      return response()->json([
        "success"=> true,
        "user" => $user,
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
}