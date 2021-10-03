<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth/admin',
    'namespace' => 'App\Http\Controllers',
    'middleware' => ['auth:api']
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('role-list', 'RoleApi@getRoleList');

      Route::get('post-list', 'AdminApi@getPostList');
      // Route::get('post-detail/{id}', 'AdminApi@getPostDetail');
    });
  });
});