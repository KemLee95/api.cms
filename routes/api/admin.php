<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth/admin',
    'namespace' => 'App\Http\Controllers',
    'middleware' => ['auth:api', 'scope:admin']
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('role-list', 'RoleApi@getRoleList');
      Route::get('account-list', 'UserApi@getUserList');
      
    });
  });
});