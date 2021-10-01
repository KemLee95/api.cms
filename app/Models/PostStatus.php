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

  public function posts() {
    return $this->hasOne(Post::class, 'post_id', 'id');
  }
}