<?php
namespace App\Models;

use App\Models\ModelBase;

use App\Models\Permission;
use App\Models\Role;

class PermissionRole extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'permission_role';
  public $timestamps = true;

  public function role() {
    return $this->hasMay(Role::class, 'id', 'role_id');
  }

  public function permission() {
    return $this->hasMany(Permission::class, 'id', 'permisstion_id');
  }
}