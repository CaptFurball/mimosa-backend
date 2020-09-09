<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use App\User;
use App\Responses\GenericResponse;

class FeedController extends Controller
{
    public function getFeed(FeedService $feedService, GenericResponse $response)
    {
        $stories = $feedService->getFeed();

        return $response->createSuccessResponse('RETRIEVED_FEED', ['stories' => $stories]);
    }

    public function getFeedByTag(FeedService $feedService, GenericResponse $response, $tag)
    {
        $this->validate(['tag' => $tag], [
            'tag' => 'required|string|exists:tags,name'
        ]);

        $stories = $feedService->getFeedByTag($tag);

        return $response->createSuccessResponse('RETRIEVED_FEED', ['stories' => $stories]);
    }

    public function getFeedByUserId(FeedService $feedService, GenericResponse $response, $userId)
    {
        $this->validate(['id' => $userId], [
            'id' => 'required|int|exists:users'
        ]);

        $stories = $feedService->getFeedByUserId($userId);

        $user = User::with(['followers'])->find($userId);

        return $response->createSuccessResponse('RETRIEVED_FEED', ['stories' => $stories, 'user' => $user]);
    }

    public function getPopularFeed(FeedService $feedService, GenericResponse $response)
    {
        $stories = $feedService->getPopularFeed();

        return $response->createSuccessResponse('RETRIEVED_FEED', ['stories' => $stories]);
    }

    public function getDiscussedFeed(FeedService $feedService, GenericResponse $response)
    {
        $stories = $feedService->getDiscussedFeed();

        return $response->createSuccessResponse('RETRIEVED_FEED', ['stories' => $stories]);
    }
}
