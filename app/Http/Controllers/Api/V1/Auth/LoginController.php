<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\V1\LoginStoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController
{
    /**
     * Store a newly created user token.
     */
    public function store(LoginStoreRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->userAgent())->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Successfully Logged In!',
        ], 201);
    }

    /**
     * Invalidate the user token.
     */
    public function destroy()
    {
        Auth::logoutCurrentDevice();
    }
}
