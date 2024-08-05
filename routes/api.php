<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('user/account/v1/users/login', 'LoginController@userLogin');
Route::post('user/account/v1/users/refresh', 'LoginController@refresh');
Route::post('user/account/v1/users/logout', 'LoginController@logout');

Route::group(['prefix' => "user"], function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('user/account/v1/users/refresh', 'LoginController@refresh');
        Route::post('user/account/v1/users/logout', 'LoginController@logout');
        Route::get('account/v1/users/lists', 'UserController@userList');
        Route::get('account/v1/users', 'UserController@index');
        Route::post('account/v1/users', 'UserController@store');
        Route::get('account/v1/users/{id}', 'UserController@show');
        Route::patch('account/v1/users/{id}', 'UserController@update');
        Route::delete('account/v1/users/{id}', 'UserController@delete');
        Route::patch('account/v1/users/{id}/password', 'UserController@changePwd');
    });

});

Route::group(['prefix' => "posts"], function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/', 'PostController@index');
        Route::post('/', 'PostController@store');
        Route::get('/list', 'PostController@list');
        Route::get('/{id}', 'PostController@show');
        Route::patch('/{id}', 'PostController@update');
        Route::delete('/{id}', 'PostController@delete');
        Route::post('/{post_id}/comments', 'CommentController@store');
        Route::patch('/{post_id}/comments/{id}', 'CommentController@update');
        Route::delete('/{post_id}/comments/{id}', 'CommentController@delete');
    });
});

Route::group(['prefix' => "categories"], function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/', 'CategoryController@index');
        Route::post('/', 'CategoryController@store');
        Route::get('/{id}', 'CategoryController@show');
        Route::patch('/{id}', 'CategoryController@update');
        Route::delete('/{id}', 'CategoryController@delete');
    });
});

Route::group(['prefix' => "tags"], function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/', 'TagController@index');
        Route::post('/', 'TagController@store');
        Route::get('/{id}', 'TagController@show');
        Route::patch('/{id}', 'TagController@update');
        Route::delete('/{id}', 'TagController@delete');
    });
});


