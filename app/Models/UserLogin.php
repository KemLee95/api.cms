<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Modles\User;

class UserLogin extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'user_login';
  public $timestamp = true;

  public function users() {
    return $this->belongsToMany(User::class, "id", "user_id");
  }

  public static function saveUserLogin($userId) {
    $userLogin = new UserLogin();
    $userLogin->user_id = $userId;
    $userLogin->save();

    return $userLogin;
  }
}