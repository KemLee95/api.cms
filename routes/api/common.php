<?php

Route::group([
  'prefix'=> 'v1',
  'namespace' => 'App\Http\Controllers',
], function(){

  Route::group([
    "prefix" => "get"
  ], function(){

    Route::get('post-list', 'PostApi@getPostList');
    Route::get('category-list', 'CategoryApi@getCategories');
    Route::get('post-detail/{id}', 'PostApi@getPostDetail');
  });

  Route::group([
    'prefix'=> 'auth',
    'middleware' => ['auth:api'],

  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('post-list', 'PostApi@getPostList');
      Route::get('reader-tracking', 'ReaderCounterApi@tracking');
      Route::get('posts-being-edited', 'PostsBeingEditedApi@edited');
      Route::get('set-editable-post', 'PostsBeingEditedApi@setEditable');
    });

    Route::group([
      'prefix'=> 'post'
    ],function(){
      Route::post('save-post', 'PostApi@save');
      Route::post('delete-post', 'PostApi@delete');
    });
  });
});