<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowerController extends Controller
{
    public function follow(GenericResponse $response, $userId)
    {
        $this->validate(['id' => $userId], [
            'id' => 'required|integer|exists:users'
        ]);

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $user->following()->create(['following' => $userId]);
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

    public function unfollow(GenericResponse $response, $userId)
    {
        $this->validate(['id' => $userId], [
            'id' => 'required|integer|exists:users'
        ]);

        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $following = $user->following()->where('following', $userId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $response->createRejectedResponse('USER_WAS_NOT_FOLLOWED');
        }

        $following->delete();

        return $response->createSuccessResponse('USER_UNFOLLOWED');
    }
}
