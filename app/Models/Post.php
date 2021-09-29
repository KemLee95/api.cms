<?php
namespace App\Models;

use App\Models\ModelBase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;


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

  public static function getPostList(Request $req) {
    $posts = Post::where("deleted_at", null);
    $posts =
    return $posts->get();
  }
}