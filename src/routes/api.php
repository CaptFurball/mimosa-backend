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
    Route::get('/user/story', 'User\PostController@list');
    Route::post('/user/post/status', 'User\PostController@status');
    Route::delete('/user/post/delete/{storyId}', 'User\PostController@delete');

    Route::post('user/follow/{userId}', 'User\FollowerController@follow');
    Route::delete('user/unfollow/{userId}', 'User\FollowerController@unfollow');

    Route::post('story/comment', 'Story\InteractionController@addComment');
    Route::delete('story/comment/delete/{commentId}', 'Story\InteractionController@removeComment');
});
