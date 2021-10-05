<?php
namespace App\Models;


use App\Models\ModelBase;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;

class RoleUser extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'role_user';
  public $timestamps = true;

  public function role() {
    return $this->hasMay(Role::class, 'id', 'role_id');
  }

  public function user() {
    return $this->hasMany(User::class, 'id', 'user_id');
  }

  public static function isExist($roleId, $userId) {
    return RoleUser::where("role_id", $roleId)->where("user_id", $userId)->exists();
  }

  public static function saveRoleUser($roleId, $userId) {
    $roleUser = new RoleUser();
    $roleUser->user_id = $userId;
    $roleUser->role_id = $roleId;
    $roleUser->save();

    return $roleUser;
  }
}