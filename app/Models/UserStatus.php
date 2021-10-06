<?php
namespace App\Models;

use App\Models\ModelBase;

class UserStatus extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'user_status';
  public $timestamps = true;

  public $DISABLED_STATE = 'disabled';
  public $ENABLED_STATE = 'enabled'; 

  public function users() {
    return $this->belongsTo(User::class, 'id', 'user_id');
  }

  public function saveUserStatus($userId, $state) {
    $newUserStatus = new UserStatus();
    $newUserStatus->user_id = $userId;
    $newUserStatus->name = $state;
    $newUserStatus->save();

    return $newUserStatus;
  }

}