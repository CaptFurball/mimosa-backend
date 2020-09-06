<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\Request;
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
}
