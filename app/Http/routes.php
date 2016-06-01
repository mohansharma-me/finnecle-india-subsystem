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

    // Channels
    Route::get('/channels', ['uses'=>'ChannelController@getIndex', 'as'=>'channels']);
    Route::post('/channels/new', ['uses'=>'ChannelController@postNewChannel', 'as'=>'create-new-channel']);
    Route::get('/channels/{channel}', ['uses'=>'ChannelController@getEditChannel', 'as'=>'edit-channel']);
    Route::post('/channels/{channel}', ['uses'=>'ChannelController@postEditChannel', 'as'=>'edit-channel-post']);
    Route::post('/channels/delete/{channel}', ['uses'=>'ChannelController@postDeleteChannel', 'as'=>'delete-channel']);

    // Draws
    Route::get('/draws', ['uses'=>'DrawController@getIndex', 'as'=>'draws']);
    Route::get('/draws/new', ['uses'=>'DrawController@getNewDraw', 'as'=>'new-draw']);
    Route::post('/draws/new', ['uses'=>'DrawController@postNewDraw', 'as'=>'new-draw-post']);
    Route::get('/draws/{draw}', ['uses'=>'DrawController@getEditDraw', 'as'=>'edit-draw']);
    Route::post('/draws/{draw}', ['uses'=>'DrawController@postEditDraw', 'as'=>'edit-draw-post']);
    Route::post('/draws/delete/{draw}', ['uses'=>'DrawController@postDeleteDraw', 'as'=>'delete-draw-post']);

    // Centers
    Route::get('/centers', ['uses'=>'CenterController@getIndex', 'as'=>'centers']);
    Route::get('/centers/new', ['uses'=>'CenterController@getNewCenter', 'as'=>'new-center']);
    Route::post('/centers/new', ['uses'=>'CenterController@postNewCenter', 'as'=>'new-center-post']);
    Route::get('/centers/{center}', ['uses'=>'CenterController@getEditCenter', 'as'=>'edit-center']);
    Route::post('/centers/{center}', ['uses'=>'CenterController@postEditCenter', 'as'=>'edit-center-post']);
    Route::post('/centers/delete/{center}', ['uses'=>'CenterController@postDeleteCenter', 'as'=>'delete-center-post']);

    ///////////////////////////////////
    //////////// DONATORS /////////////
    ///////////////////////////////////

    Route::get('/create-donation/{channel?}', ['uses'=>'DonationController@getCreateDonation', 'as'=>'create-donation']);
    Route::post('/create-donation', ['uses'=>'DonationController@postCreateDonation', 'as'=>'post-create-donation']);
    Route::get('/print-donation-slip/{transaction}', ['uses'=>'DonationController@getPrintDonationSlip', 'as'=>'print-donation-slip']);

});

Route::get('/setup', function() {

});

Route::group(['prefix'=>'api'], function() {

    Route::get('auth', ['uses'=>'ApiController@getAuth', 'as'=>'api-auth']);
    Route::post('auth', ['uses'=>'ApiController@postAuth', 'as'=>'api-auth-post']);

    Route::get('dashboard', ['uses'=>'ApiController@getDashboard', 'as'=>'api.dashboard']);

});

// 1:34 ko 21 unit