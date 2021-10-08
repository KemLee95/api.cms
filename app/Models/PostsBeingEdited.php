<?php
namespace App\Models;

use App\Models\ModelBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\User;

class PostsBeingEdited extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'posts_being_edited';
  

  public function posts() {
    return $this->belongsTo(Post::class, 'id', 'post_id');
  }

  public function users() {
    return $this->belongsTo(User::class, 'id', 'user_id');
  }

  public static function savePostsBeingEdited($postId) {
    $postsBeingEdited = new PostsBeingEdited();
    $postsBeingEdited->post_id = $postId;
    $postsBeingEdited->user_id = Auth::id();
    $postsBeingEdited->save();
    
    return  $postsBeingEdited;
  }
}