<?php

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers'
], function(){

    Route::group([
        'prefix'=> 'post',
    ], function() {

        Route::post('login', 'UserApi@login');
        Route::post('register', 'UserApi@register');
        Route::post('check-unique-user', 'UserApi@checkUniqueUser');
    });

    Route::group([
        'prefix' => 'auth/get',
        'middleware' => ['auth:api']
    ], function(){
        Route::get('user', 'UserApi@getUser');

    });
});