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

class CommonApi extends ApiBase {


  public function getCategories(Request $req) {
    \Log::info("CommonApi: get the categories");
    try {

      $categories = Category::where("deleted_at", null)->select("id", "name")->get();

      return response()->json([
        "success" => true,
        "categories" => $categories,
      ], 200);
  
    } catch (\Exception $e) {
        \Log::error("CommonApi: can't get the categories", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function savePost(Request $req) {
    // \Log::info("CommonApi: save the post");
    // try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'status' => 'required',
        'title' => 'required',
        'content' => 'required',
      ]);

      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      if($req->has('id')) {
        $post = Post::getPostDetail($req->id);

        if(Auth::user()->cannot('update', $post)) {
          return response() -> json([
            "success" => false,
            "message_title" => "Unauthorized action",
            "message" => "Please contact with administrator!",
          ],403);
        }
        
        $post = Post::savePost($req);

        return response() -> json([
          "success"=> true,
          'post' => $post,
          "message" => "Successfully!",
        ]);
      }


      return response()->json([
        "success" => true,

      ], 200);
  
    // } catch (\Exception $e) {
    //     \Log::error("CommonApi: can't save the post", ['eror message' => $e->getMessage()]);
    //     report($e);
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'An error occurred, please contact with administrator!',
    //         'message_title' => "Request failed"
    //     ], 400 );
    // }
  }
}