<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    public function createToken(LoginRequest $request)
    {
        $token = null;
        $email = $request->get('email');
        $password = $request->get('password');

        // TODO: This can be pulled into another 'DAO Layer'
        $user = User::where('email', $email)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken($email);
        }

        // TODO: Refactor maybe??
        if ($token) {
            return ['token' => $token, 'role' => $user->role];
        }

        return response()->json([
            'message' => 'Wrong credentials',
            'data' => []
        ], 401);
    }
}
