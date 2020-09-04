<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'code' => 'MALFORMED_REQUEST',
                'errors' => $validator->errors()->messages()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password, ['rounds' => 12])
        ]);
      
        event(new Registered($user));

        return response()->json([
            'code' => 'USER_CREATED',
            'message' => [
                'user' => $user
            ]
        ]);
    }
}
