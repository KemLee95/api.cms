<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Model\ReaderCounter;

class ReaderCounterDetail extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'reader_counter_detail';
  public $timestamp = true;

  public function reader_counter() {
    return $this->belongsTo(ReaderCounter::class, "id", "reader_counter_id");
  }

  public static function saveReaderCounterDetail($readerCounterId) {
    $readerCounterDetail = new ReaderCounterDetail();
    $readerCounterDetail->reader_counter_id = $readerCounterId;
    $readerCounterDetail->user_id = Auth::id();
    $readerCounterDetail->save();
    
    return $readerCounterDetail;
  }
}