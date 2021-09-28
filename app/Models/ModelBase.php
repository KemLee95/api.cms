<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelBase extends Model {
  use SoftDeletes;
  public static function getTableName() {
    
    return with(new static)-> getTable();
  }
}