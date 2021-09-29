<?php
        Route::get('post-list', 'PostApi@getPostList');

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers\home'
], function(){

    Route::group([
        'prefix' => 'auth/get',
        'middleware' => ['auth:api']
    ], function(){

        Route::get('post-list', 'PostApi@getPostList');

    });

    Route::group([
        'prefix'=> 'auth/post',
        'middleware' => ['auth:api']
    ], function() {

    });

    Route::group([
        'prefix'=>'auth/delete',
        'middleware' => ['auth:api']
    ], function() {

    });
});