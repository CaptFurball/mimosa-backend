<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Responses\GenericResponse;

class LoginController extends Controller
{
    public function login(Request $request, GenericResponse $response)
    {
        $this->validate($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]);

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
