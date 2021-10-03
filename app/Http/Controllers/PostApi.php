<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;

use App\Models\Post;
use App\Models\PostDetail;
use App\Models\PostStatus;
use App\Models\Category;


class PostApi extends ApiBase {

  public function show(Request $req) {
    $this->authorize('view', $post);
  }

  public function getPostDetail($id, Request $req) {
    \Log::info("PostApi: get the post detail");
    try {
      $input = $req->all();

      $post = Post::getPostDetail($id);
      if(Auth::user()->cannot('view', $post)) {
        return response() -> json([
          "success" => false,
          "message_title" => "Unauthorized action",
          "message" => "Please contact with administrator!",
        ],403);
      }
      $categories = Category::where("deleted_at", null)->select("id", "name")->get();
      return response()->json([
        "success"=> true,
        "post" =>$post,
        "categories" =>$categories
      ], 200);

    } catch (\Exception $e) {
        \Log::error("PostApi: can't get the post detail", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getPostList(Request $req){
    \Log::info("PostApi: get the post list");
    try {

      $pagination = Post::getPostList($req);
      $categories =  Category::where("deleted_at", null)->select("id", "name")->get();

      return response()->json([
        "success" => true,
        "pagination" => $pagination,
        "categories" =>$categories
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
  
  public function save(Request $req) {
    // \Log::info("PostApi: save the new post");
    // try {

      $input = $req->all();
      $validator = Validator::make($input, [
        'category_id' => 'required',
        'title' => 'required',
        'status' => 'required',
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
          ], 403);
        }

        $oldPostDetail = PostDetail::where("deleted_at", null)->where("post_id", $post->id)->get();
        $newPostDetail = PostDetail::savePostDetail($post->id, $req);
        if(!empty($newPostDetail)) {
          PostDetail::where("id", $oldPostDetail->id)->delete();
        }

        $oldPostStatus = PostStatus::where("deleted_at", null)->where("post_id", $post->id)->get();
        $newPostStatus = PostStatus::savePostStatus($post->id, $req);
        if(!empty($newPostStatus)) {
          PostStatus::where("id", $oldPostStatus->id)->delete();
        }
        $post->update();

        return response() -> json([
          "success"=> true,
          "message_title" => "Successful",
          "message" => "Save the new post successfully!",
        ]);
      }
      
      if(!Auth::user()->can('create', Post::class)) {
        return response() -> json([
          "success" => false,
          "message_title" => "Unauthorized action",
          "message" => "Please contact with administrator!",
        ], 403);
      }

      $newPost = Post::savePost($req);
      if(!empty($newPost)) {
        $newPostDetail = PostDetail::savePostDetail($newPost->id, $req);
        $newPostStatus = PostStatus::savePostStatus($newPost->id, $req);
      }

      return response() -> json([
        "success"=> true,
        "message_title" => "Successful",
        "message" => "Save the post successfully!",
      ]);

    // } catch (\Exception $e) {
    //     \Log::error("PostApi: can't save the new post", ['eror message' => $e->getMessage()]);
    //     report($e);
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'An error occurred, please contact with administrator!',
    //         'message_title' => "Request failed"
    //     ], 400 );
    // }
  }

  public function delete(Request $req) {
    \Log::info("PostApi: delete the post");
    try {
      $input = $req->all();

      $data = Post::getPostList($req);

      return response()->json([
        "success"=> true,
        "message_title" => "Successful!",
        "message" => "Delete the post successfully!"
      ], 200);

    } catch (\Exception $e) {
        \Log::error("PostApi: can't delete the post", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}