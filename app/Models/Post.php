<?php
namespace App\Models;

use App\Models\ModelBase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Post extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'posts';
  public $timestamp = true;
  
  const STATUS_DRAFT = 'draft';
  const STATUS_UNPUBLISHED = 'unpublished';
  const STATUS_PUBLISHED = 'published';

  public function user() {
    return $this->belongsTo(User::class, 'id', 'user_id');
  }

  public function category() {
    return $this->belongsTo(Category::class, 'id', 'category_id');
  }

  public static function getPostListForAdmin(Request $req, $paginate = 13) {
    if($req->has("paginate")) {
      $paginate = $req->paginate;
    }
    $posts = Post::where("posts.deleted_at", null)
    ->leftJoin("categories", function($join) {
      $join->on("categories.id", "posts.category_id");
    })
    ->leftJoin("users", function($join){
      $join->on("users.id", "posts.user_id");
      $join->where("users.deleted_at", null);
    })
    ->select(
      "posts.id",
      "posts.user_id",
      "posts.title",
      "posts.content",
      "posts.status",
      "posts.created_at",
      "categories.name as category_name",
      "users.name as user_name"
    );
 
    if($req->has('category_id')) {
      $posts->where("category_id", $req->category_id);
    }

    // $result = $posts->get()->filter(function($item) use ($req) {
    //   return $req->user()->can('view', $item);
    // });
    // return $result;
    return $posts->paginate($paginate);
  }
}