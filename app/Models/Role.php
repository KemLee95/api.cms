<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use App\Models\Permission;
use App\Models\User;

class Role extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'roles';
  public $timestamps = false;

  public function permissions() {
    return $this->belongsToMany(Permission::class,'permission_role', 'role_id', 'permission_id');
  }

  public function users(){
    return $this->belongsToMany(User::class,'role_user', 'user_id', 'role_id');
  }
}