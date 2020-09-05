<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    return Auth::user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/story', 'User\StoryController@list');
    Route::post('/user/story/status', 'User\StoryController@postStatus');
    Route::delete('/user/story/delete/{id}', 'User\StoryController@delete');

    Route::post('user/follow/{id}', 'User\FollowerController@add');
    Route::delete('user/unfollow/{id}', 'User\FollowerController@remove');
});
