<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/partial/{view}', function ($view) { return View::make("partial/" . $view); });
Route::get('/partial/header/{view}', function ($view) { return View::make("partial/header/" . $view); });
Route::get('/test', function(){ return view('test'); });

Route::get('/admin', function(){  });


//php-js
Route::get('/crossover.js', ['before' => 'nosession', function () { return Response::view('js.crossover-js')->header('Content-Type', 'text/javascript'); }]);

//api

#SignUp
Route::post('/api/signup/signup', 'SignUpController@signUp');
Route::post('/api/signup/checkcode', 'SignUpController@checkCode');
Route::post('/api/signup/register', 'SignUpController@register');
Route::post('/api/login/login', 'LoginController@login');
Route::post('/api/login/logout', 'LoginController@logout');

#lottery
Route::get('/api/lottery/caas', 'LotteryController@caas');
Route::get('/api/lottery/', 'LotteryController@get');
Route::post('/api/lottery/join', 'LotteryController@join');
Route::post('/api/lottery/notify', 'LotteryController@notifyResults');
//Route::post('/api/shorturls', ['uses' => 'ShortUrlsController@post']);


