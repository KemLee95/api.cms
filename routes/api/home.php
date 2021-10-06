<?php

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers'
], function(){

    Route::group([
        'prefix' => 'auth/get',
        'middleware' => ['auth:api']
    ], function(){

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