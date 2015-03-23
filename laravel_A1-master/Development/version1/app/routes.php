<?php
session_start();
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    return View::make('hello');
});

Route::get('/account/activation', function(){
    return View::make('hello');
});

Route::post('/hello', function(){
    return View::make('hello');
});

Route::get('/mailer', function()
{
    return View::make('mailerTester');
});

Route::group(array('before' => 'guest'), function(){
   
    // CSRF protection
    Route::group(array('before' => 'csrf'), function(){
        
        // post
        Route::post('/create', 'AccountController@postCreate'); 
        Route::post('/secureTest', 'AccountController@postSignin'); 
        Route::post('/forgot_password', 'AccountController@postForgotPassword');
        Route::post('/reset_password', array(
            'as' => 'reset-password-post',
            'uses' => 'AccountController@postForgotPassword'
        ));
    });
    
//    // get
//    Route::get('/create', array(
//        'as' => 'create-account',
//        'uses' => 'AccountController@getCreate'
//    ));
//    Route::get('/secureTest', array(
//        'as' => 'get-secure',
//        'uses' => 'AccountController@getSecure'
//    )); 
//    Route::get('/login', array(
//        'as' => 'sign-in',
//        'uses' => 'AccountController@getSignin'
//    ));
//    Route::get('/forgot_password', array(
//        'as' => 'forgot-password',
//        'uses' => 'AccountController@getForgotPassword'
//    ));
});

// get
    Route::get('/create', array(
        'as' => 'create-account',
        'uses' => 'AccountController@getCreate'
    ));
    Route::get('/secureTest', array(
        'as' => 'get-secure',
        'uses' => 'AccountController@getSecure'
    )); 
    Route::get('/login', array(
        'as' => 'sign-in',
        'uses' => 'AccountController@getSignin'
    ));
    Route::get('/forgot_password', array(
        'as' => 'forgot-password',
        'uses' => 'AccountController@getForgotPassword'
    ));

Route::get('/account/logout', array(
    'as' => 'account-logout',
    function(){
    Auth::logout();
    session_destroy();
    return Redirect::route('sign-in');
}));

Route::get('/reset_password/{token}/{id}', array(
        'as' => 'reset-password',
        'uses' => 'AccountController@getResetPassword'
    ));

Route::get('/account/activation/{token}', array(
    'as' => 'account-activation',
    'uses' => 'AccountController@initialActivation'
));

Route::get('/secureTest', array(
        'as' => 'authenticated-safe-zone', 
        'uses' => 'AccountController@getSecure'
));

Route::post('/reset_password', 'AccountController@postResetPassword');

Route::post('/profile/update/', array(
    'as' => 'update-profile',
    'uses' => 'ProfileController@updateProfile'
));
