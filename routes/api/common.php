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

      Route::get('category-list', 'CategoryApi@getCategories');

      Route::get('post-list', 'PostApi@getPostList');
      Route::get('post-detail/{id}', 'PostApi@getPostDetail');
    });

    Route::group([
      'prefix'=> 'post'
    ],function(){
      Route::post('save-post', 'PostApi@save');
      Route::post('delete-post', 'PostApi@delete');
    });
  });
});