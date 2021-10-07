<?php
namespace App\Models;

use App\Models\ModelBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\Category;

class PostStatus extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'post_status';
  
  const STATUS_UNPUBLISHED = 'unpublished';
  const STATUS_PUBLISHED = 'published';
  const STATUS_DRAFT = 'draft';

  public function posts() {
    return $this->hasOne(Post::class, 'post_id', 'id');
  }

  public static function savePostStatus($postId, $req) {
    $postStatus = new PostStatus();
    $postStatus->post_id = $postId;
    $postStatus->name = $req->status;
    $postStatus->save();

    return $postStatus;
  }
}