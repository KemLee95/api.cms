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

    Route::group([
      'prefix' => 'post'
    ], function(){
      Route::post('update-account', 'UserApi@updateAccount');
      Route::post('delete-account', 'UserApi@deleteAccount');
    });
  });
});