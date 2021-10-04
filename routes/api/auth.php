<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers'
], function(){
    Route::group([
        "prefix" =>"get"
    ],function(){
        Route::get("verify-email/{id}/{hash}", [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:api');;
    });

    Route::group([
        'prefix'=> 'post',
    ], function() {

        Route::post('login', 'UserApi@login');
        Route::post('register', 'UserApi@register');
        Route::post('check-unique-user', 'UserApi@checkUniqueUser');

        Route::post("email/verification-notification", [EmailVerificationController::class, 'sendVerificationEmail'])->middleware("auth:api");
        Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
        Route::post('reset-password', [NewPasswordController::class, 'reset']);
    });

    Route::group([
        'prefix' => 'auth/get',
        'middleware' => ['auth:api']
    ], function(){
        Route::get('user', 'UserApi@getUser');

    });
});