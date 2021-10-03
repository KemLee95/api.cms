<?php
namespace App\Models;

use App\Models\ModelBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostStatus;

class PostDetail extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'post_detail';
  public $timestamp = true;

  public function posts() {
    return $this->hasOne(Post::class, 'id', 'post_id');
  }

  public function post_status() {
    return $this->hasOne(PostStatus::class, 'post_id', 'post_id');
  }

  public function category() {
    return $this->belongsTo(Category::class, 'id', 'category_id');
  }

  public static function savePostDetail($postId, $req) {
    $newPostDetail = new PostDetail();
    $newPostDetail->post_id = $postId;
    $newPostDetail->title = $req->title;
    $newPostDetail->content = $req->content;
    $newPostDetail->category_id = $req->category_id;
    $newPostDetail->save();
    
    return $newPostDetail;
  }
}