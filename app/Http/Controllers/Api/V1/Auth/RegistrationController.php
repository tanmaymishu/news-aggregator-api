<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\V1\RegistrationRequest;
use App\Models\User;

class RegistrationController
{
    public function __invoke(RegistrationRequest $request)
    {
        $user = User::query()->create($request->only(['name', 'email', 'password']));

        $token = $user->createToken($request->userAgent())->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Successfully Registered!',
        ], 201);
    }
}
