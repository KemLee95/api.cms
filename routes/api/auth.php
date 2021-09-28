<?php

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers\auth'
], function(){
    Route::group([
        'prefix' => 'get'
    ], function(){
        Route::get('get-user', 'UserApi@getUser');
    });

    Route::group([
        'prefix'=> 'post'
    ], function() {

        Route::post('login', 'UserApi@login');
        Route::post('register', 'UserApi@register');
    });

    Route::group([
        'prefix'=>'delete'
    ], function() {
        Route::delete('logout', 'UserApi@logout');
    });
});