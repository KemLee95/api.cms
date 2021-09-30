<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth/',
    'namespace' => 'App\Http\Controllers',
    'middleware' => ['auth:api']
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){
      Route::get('/categories', 'CommonApi@getCategories');

    });

    Route::group([
      'prefix'=> 'post'
    ],function(){

    });
  });
});