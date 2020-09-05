<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function list(GenericResponse $response)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $stories = $user
            ->stories()
            ->orderBy('created_at', 'DESC')
            ->get();

        return $response->createSuccessResponse('USER_STORY_RETRIEVED', [
            'stories' => $stories
        ]);
    }

    public function status(Request $request, GenericResponse $response)
    {
        $this->validate($request->all(), [
            'body' => 'required|string|max:1000'
        ]);

        /** @var \App\Models\User */
        $user = Auth::user();

        $user->stories()->create([
            'body' => $request->body
        ]);

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
        $this->validate(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ]);

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
