<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\Role;
use App\Models\RoleUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\CommonHelpers;

class UserApi extends ApiBase {

  public function getUserList(Request $req) {
    \Log::info("UserApi: login");
    try {
      $input = $req->all();
      $data = User::getUserList($req);
      return response()->json([
        "success" => true,
        "data" =>$data,
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
          $userSql = User::where("deleted_at", null)->where("user_name", $input['user_name']);
          if($req->has("user_id")) {
            $userSql->where("id", "!=", $req->user_id);
          }
          $user = $userSql->count();

          if(empty($user)) {
            return response()->json([
              "success"=> true,
              "unique" => true
            ],200);
          }
          return response()->json([
            "success"=> true,
            "unique" => false
          ],200);
        }

        if($req->has('email')) {
          $userSql = User::where("deleted_at", null)->where("email", $input['email']);
          if($req->has("user_id")) {
            $userSql->where("id", "!=", $req->user_id);
          }
          $user = $userSql->count();
          if(empty($user)) {
            return response()->json([
              "success"=> true,
              "unique" => true
            ],200);
          }
          return response()->json([
            "success"=> true,
            "unique" => false
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

  public function getUserInfo(Request $req) {
    \Log::info("UserApi: get the user info");
    try {

      $validator = Validator::make($req->all(), [
        'user_id' => 'required',
      ]);

      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }
      
      $user = User::where("deleted_at", null)->find($req->user_id);
      if(!Auth::user() || Auth::user()->cannot("view", $user)) {
        return response()->json([
          "success"=>false,
          "message"=> "You don’t have permission to view!",
          "message_title" => "Request failed"
        ], 401);
      }

      $userId = $req->user_id;
      $userInfo = User::getUserInfo($userId);
      $userInfo->isAdmin = User::find($userId)->hasRole('admin');
      $roles = Role::select("id", "name")->get();
      return response()->json([
        "success" => true,
        "userInfo" => $userInfo,
        "roles" => $roles
      ]);

    } catch (\Exception $e) {
        \Log::error("UserApi: cant get the user info", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function deleteAccount(Request $req) {
    \Log::info("UserApi: get the user info");
    try {

      $validator = Validator::make($req->all(), [
        'user_id' => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 400);
      }
      $user = User::where("deleted_at", null)->find($req->user_id);
      if(Auth::user() && Auth::user()->cannot("delete", $user)) {
        return response() -> json([
          "success" => false,
          "message_title" => "Unauthorized action",
          "message" => "Please contact with administrator!",
        ], 403);
      }
      
      $oldUserStatus = UserStatus::where("deleted_at", null)->where("user_id", $req->user_id)->first();
      $newUserStatus = UserStatus::saveUserStatus($req->user_id, UserStatus::DISABLED_STATE);
      if($newUserStatus && $oldUserStatus) {
        $oldUserStatus->delete();
      }

      return response()->json([
        "success" => true,
        "message" => "Delete the user successfully!",
        "message_title" => "Successful"
      ]);
    } catch (\Exception $e) {
        \Log::error("UserApi: cant get the user info", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400);
    }
  }
  
  public function updateUserInfo(Request $req) {
    \Log::info("UserApi: update the user info");
    try {
      $validator = Validator::make($req->all(), [
        'user_id' => 'required',
        "name" => 'required',
        'user_name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'role_id' => 'required',
      ]);

      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $user =  User::find($req->user_id);
      if (!Hash::check($req->password, $user->password)) {
        return response()->json([
          "success" => false,
          "messeage" => 'Password is incorrect!',
          "message" => "Request failed"
        ],401);
      }

      if(Auth::user()->can("update", $user)) {
        $user->name = $req->name;
        $user->user_name = $req->user_name;
        
        $user->email = $req->email;
        $user->created_at = now();
        if($user->email !== $req->email) {
          $user->email = $req->email;
          $user->email_verified_at = null;
        }
        $user->save();
        $oldRoleUser = RoleUser::where("deleted_at", null)->where("user_id", $user->id)->pluck("id");
        $roleIdList = $req->role_id;
        for($idx = 0; $idx < count($roleIdList); $idx++){
          RoleUser::saveRoleUser($roleIdList[$idx], $user->id);
        }
        for($idx = 0; $idx < count($oldRoleUser); $idx++) {
          RoleUser::find($oldRoleUser[$idx])->delete();
        }
      }

      return response()->json([
        "success" => true,
        "message" => "Update user info successfuly!",
        "message_title" => "Successful"
      ],200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't update the user info", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function updateAccount(Request $req) {
    
    \Log::info("UserApi: update the user info");
    try {
      $validator = Validator::make($req->all(), [
        'user_id' => 'required',
        "name" => 'required',
        'user_name' => 'required',
        'email' => 'required|email',
        'role_id' => 'required',
      ]);

      if($validator->fails()) {
        return response()->json([
          "success"=> false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $user =  User::find($req->user_id);
      if(Auth::user()->can("update", $user)) {
        $user->name = $req->name;
        $user->user_name = $req->user_name;
        
        $user->email = $req->email;
        $user->created_at = now();
        if($user->email !== $req->email) {
          $user->email = $req->email;
          $user->email_verified_at = null;
        }
        $user->save();
        $oldRoleUser = RoleUser::where("deleted_at", null)->where("user_id", $user->id)->pluck("id");
        
        $roleIdList = $req->role_id;
        for($idx = 0; $idx < count($roleIdList); $idx++){
          RoleUser::saveRoleUser($roleIdList[$idx], $user->id);
        }
        for($idx = 0; $idx < count($oldRoleUser); $idx++) {
          RoleUser::find($oldRoleUser[$idx])->delete();
        }
      }

      return response()->json([
        "success" => true,
        "message" => "Update user info successfuly!",
        "message_title" => "Successful"
      ],200);

    } catch (\Exception $e) {
        \Log::error("UserApi: can't update the user info", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}