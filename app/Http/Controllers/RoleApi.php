<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiBase;
use Illuminate\Http\Request;
use Validator;

use App\Models\Role;

class RoleApi extends ApiBase {

  public function getRoleList(Request $req){
    \Log::info("RoleApi: get the list of the user roles");
    try {

      $roles = Role::select("id", "name")->get();
      return response()->json([
        "success" => true,
        "roles" => $roles,
      ], 200);

    } catch (\Exception $e) {
        \Log::error("RoleApi: can't get the role list", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}