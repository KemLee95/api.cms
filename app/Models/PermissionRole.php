<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;

use App\Models\Permission;
use App\Models\Role;

class PermissionRole extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'permission_role';
  public $timestamps = false;

  public function role() {
    return $this->hasMay(Role::class, 'id', 'role_id');
  }

  public function permission() {
    return $this->hasMany(Permission::class, 'id', 'permisstion_id');
  }
}