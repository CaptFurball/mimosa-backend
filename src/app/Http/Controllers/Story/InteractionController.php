<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use App\Story;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function addComment(Request $request, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'story_id' => 'required|integer|exists:stories,id',
            'body' => 'required|string|max:1000'
        ])) {
            return $this->failedValidationResponse;
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
        if (!$this->validate(['id' => $commentId], [
            'id' => 'required|integer|exists:comments',
        ])) {
            return $this->failedValidationResponse;
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
        if (!$this->validate(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ])) {
            return $this->failedValidationResponse;
        }

        try {
            Story::find($storyId)->likes()->create([
                'user_id' => Auth::user()->id
            ]);
        } catch (\Exception $e) {
             // Error code 23000: row constraint error
             if ($e->getCode() === 23000) {
                return $response->createRejectedResponse('ALREADY_LIKED');
            } else {
                return $response->createErrorResponse('DATABASE_ERROR');
            }
        }

        return $response->createSuccessResponse('LIKED');
    }

    public function removeLike(GenericResponse $response, $storyId)
    {
        if (!$this->validate(['id' => $storyId], [
            'id' => 'required|integer|exists:stories'
        ])) {
            return $this->failedValidationResponse;
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
