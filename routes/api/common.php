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
    Route::get('published-post-detail/{id}', 'PostApi@getPublishedPostDetail');
  });

  Route::group([
    'prefix'=> 'auth',
    'middleware' => ['auth:api'],
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('post-list', 'PostApi@getPostList');
      Route::get('post-detail/{id}', 'PostApi@getPostDetail');
      Route::get('reader-tracking', 'ReaderCounterApi@tracking');
      Route::get('posts-being-edited', 'PostsBeingEditedApi@edited');
      Route::get('set-editable-post', 'PostsBeingEditedApi@setEditable');

      Route::get('count-enabled-events', 'EventApi@countEnabledEvents');
      Route::get('event-partial', 'EventApi@getEventPartial');
      Route::get('users-voucher-list', 'EventApi@getVoucherList');
      Route::get('voucher-for-user', 'EventApi@getVoucherForUser');
      
    });

    Route::group([
      'prefix'=> 'post'
    ],function(){
      Route::post('save-post', 'PostApi@save');
      Route::post('delete-post', 'PostApi@delete');
    });
  });
});