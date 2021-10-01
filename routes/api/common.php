<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth',
    'middleware' => ['auth:api'],
    'namespace' => 'App\Http\Controllers',
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){
      Route::get('categories', 'CommonApi@getCategories');

    });

    Route::group([
      'prefix'=> 'post'
    ],function(){

      Route::post('/post/save', 'CommonApi@savePost');
    });
  });
});