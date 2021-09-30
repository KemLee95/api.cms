<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;

class RoleUser extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'role_user';
  public $timestamps = false;

  public function role() {
    return $this->hasMay(Role::class, 'id', 'role_id');
  }

  public function user() {
    return $this->hasMany(User::class, 'id', 'user_id');
  }

  public static function create($roleId, $userId) {
    $isExist = RoleUser::isExist($roleId, $userId);
    if(!$isExist) {
      $roleUser =  new RoleUser();
      $roleUser->role_id = $roleId;
      $roleUser->user_id = $userId;
      $roleUser->save();
      return $roleUser;
    }
  }
  public static function isExist($roleId, $userId) {
    return RoleUser::where("role_id", $roleId)->where("user_id", $userId)->exists();
  }
}