<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use App\Story;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function addComment(Request $request, GenericResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'story_id' => 'required|integer|exists:stories,id',
            'body' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        $story = Story::findOrFail($request->story_id);
        $story->comments()->create([
            'user_id' => Auth::user()->id,
            'body' => $request->body
        ]);

        return $response->createSuccessResponse('COMMENT_SUBMITTED');
    }

    public function removeComment(GenericResponse $response, $commentId)
    {
        $validator = Validator::make(['id' => $commentId], [
            'id' => 'required|integer|exists:comments',
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $comment = $user->comments()->findOrFail($commentId);
        } catch (ModelNotFoundException $e) {
            return $response->createRejectedResponse('NOT_RESOURCE_OWNER');
        }

        $comment->delete();

        return $response->createSuccessResponse('COMMENT_DELETED');
    }

    public function addLike(GenericResponse $response, $storyId)
    {
        $validator = Validator::make(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        Story::find($storyId)->likes()->create([
            'user_id' => Auth::user()->id
        ]);

        return $response->createSuccessResponse('LIKED');
    }

    public function removeLike(GenericResponse $response, $storyId)
    {
        $validator = Validator::make(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        $story = Story::find($storyId);

        try {
            $like = $story->likes()->where('user_id', Auth::user()->id)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $response->createRejectedResponse('NO_LIKE_FOUND');
        }

        $like->delete();

        return $response->createSuccessResponse('UNLIKED');
    }
}
