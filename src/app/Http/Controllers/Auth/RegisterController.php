<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Responses\GenericResponse;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request, GenericResponse $response)
    {
        $this->validate($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password, ['rounds' => 12])
        ]);
      
        return $response->createSuccessResponse('USER_CREATED');
    }
}
