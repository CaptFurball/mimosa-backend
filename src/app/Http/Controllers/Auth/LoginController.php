<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Responses\GenericResponse;

class LoginController extends Controller
{
    public function login(Request $request, GenericResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]); 

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        $user = User::where('email', $request->email)->first();

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return $response->createSuccessResponse('USER_LOGGED_IN');
        } else {
            return $response->createRejectedResponse('INVALID_CREDENTIALS');
        }
    }

    public function logout(GenericResponse $response)
    {
        Auth::logout(); 

        return $response->createSuccessResponse('USER_LOGGED_OUT');
    }
}
