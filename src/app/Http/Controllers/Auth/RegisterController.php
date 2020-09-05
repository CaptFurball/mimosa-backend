<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use Illuminate\Auth\Events\Registered;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request, GenericResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]); 

        if ($validator->fails()) {
            return $response->createMalformedRequestResponse($validator->errors()->messages());
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password, ['rounds' => 12])
        ]);
      
        return $response->createSuccessResponse('USER_CREATED');
    }
}
