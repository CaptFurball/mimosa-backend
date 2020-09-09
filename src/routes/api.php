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
    return response()->json([
        'status' => 'SUCCESS',
        'code' => 'USER_RETRIEVED',
        'message' => [
            'user' => Auth::user()
        ]
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/story', 'User\PostController@list');
    Route::post('/user/post/status', 'User\PostController@status');
    Route::post('/user/post/link', 'User\PostController@link');
    Route::post('/user/post/photo', 'User\PostController@photo');
    Route::post('/user/post/video', 'User\PostController@video');
    Route::post('/user/post/share', 'User\PostController@share');
    Route::delete('/user/post/delete/{storyId}', 'User\PostController@delete');

    Route::post('user/follow/{userId}', 'User\FollowerController@follow');
    Route::delete('user/unfollow/{userId}', 'User\FollowerController@unfollow');

    Route::post('story/comment', 'Story\InteractionController@addComment');
    Route::delete('story/comment/delete/{commentId}', 'Story\InteractionController@removeComment');

    Route::post('story/like/{storyId}', 'Story\InteractionController@addLike');
    Route::delete('story/unlike/{storyId}', 'Story\InteractionController@removeLike');

    Route::get('/story', 'Story\FeedController@getFeed');
    Route::get('/story/tag/{tag}', 'Story\FeedController@getFeedByTag');
    Route::get('/story/user/{userId}', 'Story\FeedController@getFeedByUserId');
    Route::get('/story/popular', 'Story\FeedController@getPopularFeed');
    Route::get('/story/discussed', 'Story\FeedController@getDiscussedFeed');
});
