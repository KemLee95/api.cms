<?php
namespace App\Http\Controllers\home;

use App\Http\Controllers\ApiBase;
use Illuminate\Http\Request;
use Validator;

use App\Models\Post;

class PostApi extends ApiBase {

  public function show(Request $req) {
    $this->authorize('view', $post);
  }

  public function getPostList(Request $req){
    \Log::info("PostApi: get the post list");
    try {
      $input = $req->all();

      $data = Post::getPostList($req);

      return response()->json([
        "success"=> true,
        "data" =>$data
      ], 200);

    } catch (\Exception $e) {
        \Log::error("PostApi: can't get the post list", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}