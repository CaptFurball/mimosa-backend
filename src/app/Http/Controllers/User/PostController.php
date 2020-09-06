<?php

namespace App\Http\Controllers\User;

use App\Tag;
use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    public function status(Request $request, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'body' => 'required|string|max:1000',
            'tags' => 'string|max:1000'
        ])) {
            return $this->failedValidationResponse;
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        $story = $user->stories()->create([
            'body' => $request->body
        ]);

        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);

            foreach ($tags as $tag) {
                try {
                    $existingTag = Tag::where('name', $tag)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    $existingTag = Tag::create(['name' => $tag]);
                }

                $story->tags()->attach($existingTag->id);
            }
        }

        return $response->createSuccessResponse('STORY_POSTED');
    }
    
    public function photo(Request $request)
    {
        
    }
    
    public function video(Request $request)
    {

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
