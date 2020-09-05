<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Story;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'story_id' => 'required|integer|exists:stories,id',
            'body' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'error' => $validator->errors()->messages()
            ]);
        }

        $story = Story::find($request->story_id);
        $story->comments()->create([
            'user_id' => Auth::user()->id,
            'body' => $request->body
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'COMMENT_SUBMITTED',
        ]);
    }

    public function removeComment($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:comments',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'error' => $validator->errors()->messages()
            ]);
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $comment = $user->comments()->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'NOT_OWNER_OF_RESOURCE',
            ]);
        }

        $comment->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'COMMENT_DELETED'
        ]);
    }

    public function addLike($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:stories'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'errors' => $validator->errors()->messages()
            ]);
        }

        $story = Story::find($id);

        try {
            $story->likes()->create([
                'user_id' => Auth::user()->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'code' => 'DATABASE_ERROR',
            ]);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'LIKED_POST'
        ]);
    }

    public function removeLike($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:stories'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'errors' => $validator->errors()->messages()
            ]);
        }

        $story = Story::find($id);

        try {
            $like = $story->likes()->where('user_id', Auth::user()->id)->first();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'code' => 'DATABASE_ERROR',
            ]);
        }

        $like->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'UNLIKED_POST'
        ]);
    }
}
