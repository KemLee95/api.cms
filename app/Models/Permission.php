<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use App\Models\Role;

class Permission extends ModelBase {
  
  public $connection = 'mysql';
  public $table = 'permissions';
  public $timestamps = false;

  public function roles() {
    return $this->belongsToMany(Role::class, 'permission_role', 'role_id', 'permission_id');
  }
}