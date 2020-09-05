<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function list()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'USER_STORY_RETRIEVED',
            'message' => [
                'stories' => $user
                    ->stories()
                    ->orderBy('created_at', 'DESC')
                    ->get(),
            ]
        ]);
    }

    public function postStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'errors' => $validator->errors()->messages()
            ]);
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        $user->stories()->create([
            'body' => $request->body
        ]);
        
        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'STORY_POSTED'
        ]);
    }
    
    public function postPhoto(Request $request)
    {
        
    }
    
    public function postVideo(Request $request)
    {

    }

    public function delete($id)
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

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $story = $user->stories()->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'NOT_OWNER_OF_RESOURCE',
            ]);
        }

        $story->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'STORY_POST_DELETED'
        ]);
    }
}
