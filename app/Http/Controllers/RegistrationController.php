<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Response\HttpResponse;
use App\Models\User;
use App\Utils\Constants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function register(RegisterRequest $request) {
        $email = $request->get('email');
        $user = new User($request->all());
        $user->password = Hash::make($request->get('password'));
        $user->save();

        $token = $user->createToken($email);

        return HttpResponse::response('Success', ['token' => $token, 'role' => Constants::USER_ROLE_USER]);
    }
}
