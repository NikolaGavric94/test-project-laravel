<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Response\HttpResponse;
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
            // Instead of creating token each time, we can check if
            // there is already existing token and send that one, until it expires
            $token = $user->createToken($email);
            return HttpResponse::response('Success', ['token' => $token, 'role' => $user->role]);
        }

        return response()->json(HttpResponse::response('Wrong credentials', [], 401), 401);
    }
}
