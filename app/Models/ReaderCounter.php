<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Model\Post;
use App\Model\ReaderCounterDetail;

class ReaderCounter extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'reader_counter';
  public $timestamp = true;

  public function posts() {
    return $this->belongsToMany(Post::class, "id", "post_id");
  }

  public function reader_counter_detail() {
    return $this->hasOne(ReaderCounterDetail::class, "reader_counter_id", "id");
  }

  public static function saveReaderCounter($postId) {
    $readerCounter = new ReaderCounter();
    $readerCounter->post_id = $postId;
    $readerCounter->save();

    return $readerCounter;
  }
}