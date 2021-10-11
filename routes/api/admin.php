<?php

Route::group([
  'prefix'=> 'v1'
], function(){

  Route::group([
    'prefix'=> 'auth/admin',
    'namespace' => 'App\Http\Controllers',
    'middleware' => ['auth:api', 'scope:admin']
  ], function(){

    Route::group([
      'prefix'=> 'get'
    ],function(){

      Route::get('role-list', 'RoleApi@getRoleList');
      Route::get('account-list', 'UserApi@getUserList');

      Route::get('event-list', 'EventApi@getEventList');
      Route::get('event-detail', 'EventApi@getEventDetail');
      Route::get('voucher-partial', 'EventApi@getVoucherPartial');
      Route::get('voucher-detail', 'EventApi@getVoucherDetail');
      Route::get('voucher-users', 'EventApi@getVoucherUsers');
    });

    Route::group([
      'prefix' => 'post'
    ], function(){
      Route::post('update-account', 'UserApi@updateAccount');
      Route::post('delete-account', 'UserApi@deleteAccount');
      Route::post('save-event', 'EventApi@saveEvent');
    });
  });
});