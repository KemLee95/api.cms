<?php
namespace App\Models;

use App\Models\ModelBase;
use App\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends ModelBase {
  
  use SoftDeletes;

  public $connection = 'mysql';
  public $table = 'categories';

  public function post() {
    return $this->hasMany(Post::class, 'category_id', 'id');
  }
}