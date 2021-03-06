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

    // Settings
    Route::get('/settings', ['uses'=>'GeneralSettingController@getIndex', 'as'=>'general-settings']);
    Route::post('/settings', ['uses'=>'GeneralSettingController@postSaveSettings', 'as'=>'general-settings-post']);

    // Reports
    Route::group(['prefix'=>'reports', 'namespace'=>'ReportControllers'], function() {
        Route::get('/declarations', ['uses'=>'DeclarationReportController@getIndex', 'as'=>'reports-declarations']);
        Route::get('/paid-transactions', ['uses'=>'PaidTransactionReportController@getIndex', 'as'=>'reports-paid-transactions']);
        Route::get('/donations', ['uses'=>'DonationReportController@getIndex', 'as'=>'reports-donations']);
    });

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

    // Declare
    Route::get('/declare-ngo', ['uses'=>'DeclarationController@getDeclareNgo', 'as'=>'declare-ngo']);
    Route::get('/declare-ngo/{channel}', ['uses'=>'DeclarationController@getDeclareNgo_channel', 'as'=>'declare-ngo-channel']);
    Route::get('/declare-ngo/{channel}/{draw}', ['uses'=>'DeclarationController@getDeclareNgo_channel_draw', 'as'=>'declare-ngo-channel-draw']);
    Route::get('/declare-ngo/{channel}/{draw}/{ngo}', ['uses'=>'DeclarationController@getDeclareNgo_channel_draw_ngo', 'as'=>'declare-ngo-channel-draw-ngo']);
    Route::post('/declare-ngo/{channel}/{draw}/{ngo}', ['uses'=>'DeclarationController@postDeclareNgo_channel_draw_ngo', 'as'=>'post-declare-ngo']);

    // Clear Requests
    Route::get('/clear-request/accept/{clearRequest}', ['uses'=>"ClearRequestController@getAcceptRequest", "as"=>'accept-clear-request']);
    Route::get('/clear-request/reject/{clearRequest}', ['uses'=>"ClearRequestController@getRejectRequest", "as"=>'reject-clear-request']);

    ///////////////////////////////////
    //////////// DONATORS /////////////
    ///////////////////////////////////

    Route::get('/create-donation/{channel?}', ['uses'=>'DonationController@getCreateDonation', 'as'=>'create-donation']);
    Route::post('/create-donation', ['uses'=>'DonationController@postCreateDonation', 'as'=>'post-create-donation']);
    Route::get('/donations', ['uses'=>'DonationController@getDonations', 'as'=>'donations']);
    Route::get('/print-donation-slip/{transaction}', ['uses'=>'DonationController@getPrintDonationSlip', 'as'=>'print-donation-slip']);
    Route::post('/clear-request', ['uses'=>'DonationController@postClearRequest', 'as'=>'post-clear-request']);

    ///////////////////////////////////
    ///////////// CASHIER /////////////
    ///////////////////////////////////

    Route::get('/check-donation/{transaction}', ['uses'=>'DonationController@getCheckDonation', 'as'=>'check-donation']);
    Route::post('/check-donation', ['uses'=>'DonationController@postAjaxDonation', 'as'=>'ajax-check-donation']);
    Route::post('/check-donation/paid', ['uses'=>'DonationController@postPaidDonation', 'as'=>'paid-donation']);
    Route::get('/cashier/clear-requests', ['uses'=>'DonationController@getCashierClearRequests', 'as'=>'cashier-clear-requests']);
    Route::post('/cashier/clear-request', ['uses'=>'DonationController@postCashierClearRequest', 'as'=>'post-cashier-clear-request']);
    Route::get('/cashier/paid-transactions', ['uses'=>"DonationController@getCashierPaidDonations", "as"=>"cashier-paid-donations"]);

});

Route::get('/setup', function() {
    return Illuminate\Support\Facades\DB::select(Illuminate\Support\Facades\DB::raw("select * from users"));
});

Route::group(['prefix'=>'api'], function() {

    Route::get('auth', ['uses'=>'ApiController@getAuth', 'as'=>'api-auth']);
    Route::post('auth', ['uses'=>'ApiController@postAuth', 'as'=>'api-auth-post']);

    Route::get('dashboard', ['uses'=>'ApiController@getDashboard', 'as'=>'api.dashboard']);

});

// 1:34 ko 21 unit