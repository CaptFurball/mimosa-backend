<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function add($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users'
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
            $user->following()->create([
                'following' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'code' => 'DATABASE_ERROR',
            ]);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'USER_FOLLOWED'
        ]);
    }

    public function remove($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users'
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
            $following = $user->following()->where('following', $id)->first();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'USER_NOT_FOLLOWED',
            ]);
        }

        $following->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'USER_UNFOLLOWED'
        ]);
    }
}
