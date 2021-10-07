<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::group([
    'prefix'=> "v1",
    'namespace' => 'App\Http\Controllers'
], function(){

    Route::post("/email/verification-notification", [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware(["auth:api", "throttle:6,1"])->name("verification.send");

    Route::get("/verify-email/{id}/{hash}", [EmailVerificationController::class, 'verify'])
        ->middleware(['signed'])->name('verification.verify');
    
    Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword'])
    ->middleware("guest")->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'reset'])
    ->middleware('guest')->name('password.reset');

    Route::group([
        'prefix'=> 'post',
    ], function() {

        Route::post('login', [AuthController::class, 'login'])->name("login");
        Route::post('register', [AuthController::class, 'register']);

        Route::post('check-unique-user', 'UserApi@checkUniqueUser');
    });

    Route::group([
        'prefix' => 'auth/get',
        'middleware' => ['auth:api']
    ], function(){
        Route::get("user-info", 'UserApi@getUserInfo');
        
    });
    Route::group([
        'prefix' => 'auth/post',
        'middleware' => ['auth:api']
    ], function(){
        Route::post('update-user-info', 'UserApi@updateUserInfo');
    });
});