<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder {

  public function run() {

    DB::table('permissions')->insert([
        ['name' => 'review_post'],
        ['name' => 'create_post'],
        ['name' => 'update_post'],
        ['name' => 'delete_post'],
        ['name' => 'restore_post'],
        ['name' => 'force_delete_post'],


        ['name' => 'create_user'],
        ['name' => 'update_user'],
        ['name' => 'delete_user'],

        ['name' => 'create_role'],
        ['name' => 'update_role'],
        ['name' => 'delete_role'],
    ]);

    DB::table('roles')->insert([
      ['name' => 'admin'],
      ['name' => 'user'],
    ]);

    DB::table('role_user')->insert([
            ['role_id' => 1,'user_id' => 1],
            ['role_id' => 2,'user_id' => 2]
        ]
    );

    DB::table('permission_role')->insert([
        ['permission_id' => 1, 'role_id' => 1],
        ['permission_id' => 2, 'role_id' => 1],
        ['permission_id' => 3, 'role_id' => 1],
        ['permission_id' => 4, 'role_id' => 1],
        ['permission_id' => 5, 'role_id' => 1],

        ['permission_id' => 6, 'role_id' => 1],
        ['permission_id' => 7, 'role_id' => 1],
        ['permission_id' => 8, 'role_id' => 1],
        ['permission_id' => 9, 'role_id' => 1],
        ['permission_id' => 10, 'role_id' => 1],
        ['permission_id' => 11, 'role_id' => 1],
        ['permission_id' => 12, 'role_id' => 1],
        ['permission_id' => 1, 'role_id' => 2],
    ]);
  }
}