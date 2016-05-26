<?php

Route::get('/test', function() {
   echo bcrypt('admin');
});

// Public Routes
Route::get('/', ['uses'=>'UserController@getIndex', 'as'=>'index']);
Route::post('/login', ['uses'=>'UserController@postLogin', 'as'=>'login']);
Route::get('/forgot-password', ['uses'=>'UserController@getForgotPassword', 'as'=>'forgot-password']);


// Authenticated Routes & prefixed with /dashboard
Route::group(['middleware'=>['auth'], 'prefix'=>'dashboard'], function() {

    Route::get('/', [ 'uses'=>'UserController@getDashboard', 'as'=>'dashboard']);
    Route::get('/logout', ['uses'=>'UserController@getLogout', 'as'=>'logout']);

});