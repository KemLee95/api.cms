<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth/admin',
    'namespace' => 'App\Http\Controllers\admin',
    'middleware' => ['auth:api']
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('/role-list', 'AdminApi@getRoleList');
      Route::get('/post-list', 'AdminApi@getPostList');
      
    });

    Route::group([
      'prefix'=> 'post'
    ],function(){

      Route::post('/register', 'AdminApi@register');
    });
  });
});