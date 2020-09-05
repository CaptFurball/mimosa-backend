<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'status' => 'REJECTED',
                'code' => 'MALFORMED_REQUEST',
                'errors' => $validator->errors()->messages()
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return response()->json([
                'status' => 'SUCCESS',
                'code' => 'USER_LOGGED_IN',
                'message' => [
                    'user' => $user
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'NOT_FOUND',
                'code' => 'INVALID_CREDENTIALS'
            ]);
        }
    }

    public function logout()
    {
        Auth::logout(); 

        return response()->json([
            'code' => 'USER_LOGGED_OUT'
        ]);
    }
}
