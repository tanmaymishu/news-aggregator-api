<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\V1\LoginStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController
{
    /**
     * Authenticate user and create token/session.
     *
     * @param LoginStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function store(LoginStoreRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            return response()->json([
                'data' => [
                    'user' => $user,
                    'access_token' => $user->createToken($request->userAgent())->plainTextToken,
                    'token_type' => 'bearer',
                ],
                'message' => 'Successfully Logged In!',
            ], 201);
        } else {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }
    }

    /**
     * Log out the user and invalidate session.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        } else {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->noContent();
    }
}
