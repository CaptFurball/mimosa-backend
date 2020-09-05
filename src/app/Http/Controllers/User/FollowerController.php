<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowerController extends Controller
{
    public function follow(GenericResponse $response, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users'
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $user->following()->create(['following' => $id]);
        } catch (\Exception $e) {
            // Error code 23000: row constraint error
            if ($e->getCode() == 23000) {
                return $response->createRejectedResponse('ALREADY_FOLLOWED');
            } else {
                return $response->createErrorResponse('DATABASE_ERROR');
            }
        }

        return $response->createSuccessResponse('USER_FOLLOWED');
    }

    public function unfollow(GenericResponse $response, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users'
        ]);

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $following = $user->following()->where('following', $id)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $response->createRejectedResponse('USER_WAS_NOT_FOLLOWED');
        }

        $following->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 'USER_UNFOLLOWED'
        ]);
    }
}
