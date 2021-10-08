<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\Post;
use App\Models\PostStatus;
use App\Models\PostsBeingEdited;

class PostsBeingEditedApi extends ApiBase {

  public function edited(Request $req) {
    \Log::info("ReaderCounterApi: track the post");
    try {

      $input = $req->all();
      $validator = Validator::make($input, [
        'post_id' => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $olds = PostsBeingEdited::where("deleted_at", null)->where("post_id", $req->post_id)->get();
      if(count($olds) ) {
        return response()->json([
          "success" => false,
          "message" => "The post is still being edited by an other.",
          "message_title" => "Request failed"
        ], 200);
      }
      
      $postsBeindEdited = PostsBeingEdited::savePostsBeingEdited($req->post_id);
      return response()->json([
        "success" => true,
        "message" => "The post is being edited",
        "message_title" => "Successful"
      ], 200);

    } catch (\Exception $e) {
        \Log::error("ReaderCounterApi: can't track the post", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function setEditable(Request $req) {
    \Log::info("ReaderCounterApi: track the post");
    try {

      $input = $req->all();
      $validator = Validator::make($input, [
        'post_id' => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }
      

      $olds = PostsBeingEdited::where("deleted_at", null)->where("post_id", $req->post_id)->first();
      
      if($olds ) {
          if($olds->user_id === Auth::id()) {
            $olds->delete();
          } else {
            return response()->json([
              "success" => false,
              "message" => "The post is still being edited by an other",
              "message_title" => "Request failed"
            ], 400);
          }
      }

      return response()->json([
        "success" => true,
        "message" => "The post is editable",
        "message_title" => "Successful"
      ], 200);

    } catch (\Exception $e) {
        \Log::error("ReaderCounterApi: can't track the post", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}