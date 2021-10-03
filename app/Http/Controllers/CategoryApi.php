<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Post;
use App\Models\Category;

class CategoryApi extends ApiBase {


  public function getCategories(Request $req) {
    \Log::info("CategoryApi: get the categories");
    try {

      $categories = Category::where("deleted_at", null)->select("id", "name")->get();

      return response()->json([
        "success" => true,
        "categories" => $categories,
      ], 200);
  
    } catch (\Exception $e) {
        \Log::error("CategoryApi: can't get the categories", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
  
}