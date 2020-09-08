<?php

namespace App\Http\Controllers\User;

use App\Tag;
use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\PostService;

class PostController extends Controller
{
    public function list(GenericResponse $response)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return $response->createSuccessResponse('USER_STORY_RETRIEVED', [
            'stories' => $user->stories
        ]);
    }

    public function status(Request $request, PostService $postService, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'body' => 'required|string|max:1000',
            'tags' => 'string|max:1000'
        ])) {
            return $this->failedValidationResponse;
        }

        $postService->post($request->body, $request->has('tags')? $request->tags: '');

        return $response->createSuccessResponse('STORY_POSTED');
    }
    
    public function photo(Request $request, PostService $postService, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'body' => 'required|string|max:1000',
            'photo' => 'required|image|max:1024',
            'tags' => 'string|max:1000'
        ])) {
            return $this->failedValidationResponse;
        }

        $postService->postPhoto($request->body, $request->file('photo'), $request->has('tags')? $request->tags: '');

        return $response->createSuccessResponse('STORY_POSTED');
    }
    
    public function video(Request $request, PostService $postService, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'body' => 'required|string|max:1000',
            'video' => 'required|file|max:10240',
            'tags' => 'string|max:1000'
        ])) {
            return $this->failedValidationResponse;
        }

        $allowedMimeType = [
            'video/mp4',
            'video/x-flv',
            'video/3gpp',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
        ];

        $mimeType = $request->video->getMimeType();

        if (!in_array($mimeType, $allowedMimeType)) {
            return $response->createRejectedResponse('VIDEO_FORMAT_IS_NOT_SUPPORTED');
        }

        $postService->postVideo($request->body, $request->file('video'), $request->has('tags')? $request->tags: '');

        return $response->createSuccessResponse('STORY_POSTED');
    }

    public function link(Request $request, PostService $postService, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'body' => 'required|string|max:1000',
            'tags' => 'string|max:1000',
            'url'  => 'required|url|max:255'
        ])) {
            return $this->failedValidationResponse;
        }

        $postService->postLink($request->body, $request->url, $request->has('tags')? $request->tags: '');

        return $response->createSuccessResponse('STORY_POSTED');
    }

    public function delete(GenericResponse $response, $storyId)
    {
        if (!$this->validate(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ])) {
            return $this->failedValidationResponse;
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $story = $user->stories()->findOrFail($storyId);
        } catch (ModelNotFoundException $e) {
            return $response->createRejectedResponse('NOT_STORY_OWNER');
        } 

        $story->delete();

        return $response->createSuccessResponse('STORY_DELETED');
    }
}
