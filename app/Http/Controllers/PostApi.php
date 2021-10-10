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
use App\Models\User;
use App\Models\PostsBeingEdited;


class PostApi extends ApiBase {

  public function show(Request $req) {
    $this->authorize('view', $post);
  }

  public function getPostDetail($id, Request $req) {
    \Log::info("PostApi: get the post detail");
    try {
      $input = $req->all();

      $post = Post::getPostDetail($id);

      if(Auth::user() && Auth::user()->cannot('view', $post)) {
        return response() -> json([
          "success" => false,
          "message_title" => "Unauthorized action",
          "message" => "Please contact with administrator!",
        ],403);
      }

      if(!$post) {
        return response()->json([
          "success" => false,
          "message" => "The post is not exist",
          "message_title" => "Request failed"
        ]);
      }
      $canUpdate = Auth::user() && Auth::user()->can('update', $post) || $post->status == PostStatus::STATUS_DRAFT;
      $editabled = !PostsBeingEdited::where("deleted_at", null)->where("post_id", $id)->first();

      return response()->json([
        "success"=> true,
        "post" =>$post,
        "canUpdate" => $canUpdate && $editabled
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

  public function getPublishedPostDetail($id, Request $req) {
    \Log::info("PostApi: get the post detail");
    try {
      $input = $req->all();

      $post = Post::getPostDetail($id);

      if($post->status == 'unpublished') {
        return response() -> json([
          "success" => false,
          "message" => "Please login first to access the post", 
          "message_title" => "Request failed"
        ], 403);
      }

      if(!$post) {
        return response()->json([
          "success" => false,
          "message" => "The post is not exist",
          "message_title" => "Request failed"
        ]);
      }

      return response()->json([
        "success"=> true,
        "post" =>$post,
        "canUpdate" => false
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

      return response()->json([
        "success" => true,
        "pagination" => $pagination,
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
    \Log::info("PostApi: save the new post");
    try {

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

        if(Auth::user()->cannot('update', $post) && $post->status !== PostStatus::STATUS_DRAFT) {
          return response() -> json([
            "success" => false,
            "message_title" => "Unauthorized action",
            "message" => "Please contact with administrator!",
          ], 403);
        }

        $postsBeingEdited = PostsBeingEdited::where("deleted_at", null)->where("post_id", $req->id)->first();
        if(!empty($postsBeingEdited) && Auth::id() !== $postsBeingEdited->user_id) {
          return response()->json([
            "success" => false,
            "message" => "The post is being edited by an other!",
            "message_title" => "Request failed"
          ], 409);
        }

        if($post->status === PostStatus::STATUS_DRAFT && $req->status !== PostStatus::STATUS_DRAFT 
          && Auth::id() !== $post->user_id) {
            return response()->json([
              "success" => false,
              "message" => "You don't have the permisson to change the status of the post",
              "message_title" => "Request failed"
            ], 409);
        }

        $oldPostDetail = PostDetail::where("deleted_at", null)->where("post_id", $post->id)->first();
        $newPostDetail = PostDetail::savePostDetail($post->id, $req);
        if(!empty($newPostDetail)) {
          PostDetail::find($oldPostDetail->id)->delete();
        }
        $oldPostStatus = PostStatus::where("deleted_at", null)->where("post_id", $post->id)->first();
        $newPostStatus = PostStatus::savePostStatus($post->id, $req);
        if(!empty($newPostStatus)) {
          PostStatus::find($oldPostStatus->id)->delete();
        }
        $post->update();

        if(!empty($postsBeingEdited)) {
          $postsBeingEdited->delete();
        }

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
          "message" => "Please verify email before creating your posts!",
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

    } catch (\Exception $e) {
        \Log::error("PostApi: can't save the new post", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function delete(Request $req) {
    \Log::info("PostApi: delete the post");
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

      $post = Post::where("deleted_at", null)->where("id", $req->post_id)->first();
      if(Auth::user()->cannot('delete', $post)) {
        return response() -> json([
          "success" => false,
          "message_title" => "Unauthorized action",
          "message" => "Please contact with administrator!",
        ], 403);
      }

      $postDetail = PostDetail::where("deleted_at", null)->where("post_id", $req->post_id)->first();
      $postStatus = PostStatus::where("deleted_at", null)->where("post_id", $req->post_id)->first();

      if($post && $postDetail && $postStatus) {
        Post::find($post->id)->delete();
        PostDetail::find($postDetail->id)->delete();
        PostStatus::find($postStatus->id)->delete();
        return response()->json([
          "success"=> true,
          "message_title" => "Successful!",
          "message" => "Delete the post successfully!"
        ], 200);
      }
      return response()->json([
        "success" => false,
        "message" => "Can not get the data for this post, Please contact with administrator!",
        "message_title" => "Request failed"
      ], 400);

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