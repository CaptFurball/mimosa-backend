<?php

namespace App\Http\Controllers\Auth;

use App\Events\AppActivity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Responses\GenericResponse;

class LoginController extends Controller
{
    public function login(Request $request, GenericResponse $response)
    {
        if (!$this->validate($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ])) {
            return $this->failedValidationResponse;
        };

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            event(new AppActivity(Auth::user()->name . ' has logged in!'));

            return $response->createSuccessResponse('USER_LOGGED_IN', ['user' => Auth::user()]);
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
