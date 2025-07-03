<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\V1\RegistrationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

class RegistrationController
{
    /**
     * Register a new user.
     *
     * @unauthenticated
     */
    public function __invoke(RegistrationRequest $request): JsonResponse
    {
        $user = User::query()->create($request->only(['name', 'email', 'password']));

        $token = $user->createToken($request->userAgent())->plainTextToken;

        event(new Registered($user));

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
