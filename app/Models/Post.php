<?php
namespace App\Models;

use App\Models\ModelBase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\PostStatus;
use App\Models\ReaderCounter;
use Carbon\Carbon;
use App\Models\PostsBeingEdited;
class Post extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'posts';
  public $timestamp = true;
  
  public function post_status() {
    return $this->hasOne(PostStatus::class, 'post_id', 'id');
  }

  public function user() {
    return $this->belongsTo(User::class, 'id', 'user_id');
  }

  public function reader_counter() {
    return $this->hasMany(ReaderCounter::class, "post_id", "id");
  }

  public function editing_user() {
    return $this->hasOneThrough(User::class, PostsBeingEdited::class, 'post_id', 'id', 'id', 'user_id');
  }

  public static function getPostList(Request $req, $paginate = 5) {

    if($req->has("paginate")) {
      $paginate = $req->paginate;
    }
    
    $posts = Post::where("posts.deleted_at", null)
    ->leftJoin("users", function($join){
      $join->on("users.id", "posts.user_id");
      $join->where("users.deleted_at", null);
    })
    ->leftJoin('post_status', function($join) {
      $join->on('post_status.post_id', 'posts.id');
      $join->where('post_status.deleted_at', null);
    })
    ->leftJoin('post_detail', function($join){
      $join->on('post_detail.post_id', 'posts.id');
      $join->where('post_detail.deleted_at', null);
      $join->leftJoin("categories", function($join){
        $join->on("categories.id", "post_detail.category_id");
        $join->where("categories.deleted_at", null);
      });
    })
    ->select(
      "posts.id",
      "posts.user_id",
      "users.name as user_name",
      "post_detail.title as title",
      "post_detail.content as content",
      "post_status.name as status",
      "posts.created_at",
      "post_detail.created_at as updated_at",
      "categories.id as category_id",
      "categories.name as category_name",
    )
    ->addSelect(\DB::raw("(select count(*) from reader_counter where reader_counter.post_id = posts.id) as views"));
 
    if($req->has('category_id')) {
      $posts->where("categories.id", $req->category_id);
    }

    if(Auth::user() && Auth::user()->hasRole('admin')) {

      return $posts->paginate($paginate);
    } elseif(Auth::user()) {

      $posts->where("posts.user_id", Auth::id())
      ->orWhereHas("post_status", function($sql){
        $sql->where("post_status.deleted_at", null);
        $sql->where("post_status.name", PostStatus::STATUS_PUBLISHED);
        $sql->orWhere("post_status.name", PostStatus::STATUS_DRAFT);

      });
    }

    $posts->whereHas("post_status", function($sql){
      $sql->where("post_status.deleted_at", null);
      $sql->where("post_status.name", PostStatus::STATUS_PUBLISHED);
      $sql->orWhere("post_status.name", PostStatus::STATUS_DRAFT);
    });
    return $posts->paginate($paginate);
  }

  public static function getPostDetail($id) {
    $post = Post::where("posts.deleted_at", null)
    ->where("posts.id", $id)
    ->leftJoin("users", function($join){
      $join->on("users.id", "posts.user_id");
      $join->where("users.deleted_at", null);
    })
    ->leftJoin('post_status', function($join) {
      $join->on('post_status.post_id', 'posts.id');
      $join->where('post_status.deleted_at', null);
    })
    ->leftJoin('post_detail', function($join){
      $join->on('post_detail.post_id', 'posts.id');
      $join->where('post_detail.deleted_at', null);
      $join->leftJoin("categories", function($join){
        $join->on("categories.id", "categories.id");
        $join->where("categories.deleted_at", null);
      });
    })
    ->with("editing_user", function($sql){
      $sql->where("users.deleted_at", null);
      $sql->select('users.id', "users.user_name");
    })
    ->select(
      "posts.id",
      "posts.user_id",
      "users.name as user_name",
      "post_detail.title as title",
      "post_detail.content as content",
      "post_status.name as status",
      "posts.created_at",
      "post_detail.created_at as updated_at",
      "categories.id as category_id",
      "categories.name as category_name",
    )->first();
    
    return $post;
  }

  public static function savePost($req) {
    
    $newPost = new Post;
    $newPost->user_id = Auth::id();
    $newPost->save();

    return $newPost;
  }

  public static function noReaderPost() {

    $start = (new Carbon('now'))->hour(0)->minute(0)->second(0);
    $end = (new Carbon('now'))->hour(23)->minute(59)->second(59);

    $noReaderPosts = Post::where("posts.deleted_at", null)
    ->leftJoin("post_detail", function($join) {
      $join->where("post_detail.deleted_at", null);
      $join->on("post_detail.post_id", "posts.id");
    })
    ->leftJoin("users", "users.id", "=", "posts.user_id")
    ->whereDoesntHave("reader_counter", function($sql) use($start, $end) {
      $sql->whereBetween("reader_counter.created_at", [$start , $end]);
    })
    ->select(
      "posts.id as post_id",
      "posts.user_id",
      "post_detail.title",
      "users.email as user_email",
      "users.name as user_name"
    );
    
    return $noReaderPosts->get();
  }
}