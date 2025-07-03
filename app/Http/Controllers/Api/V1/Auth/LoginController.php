<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\V1\LoginStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginController
{
    /**
     * Authenticate user and create token/session.
     *
     * This endpoint is both mobile and SPA-safe.
     * Meaning, if a React/Vue app hits this route after
     * /sanctum/csrf-cookie is hit, the returned token
     * will not be stored on the browser for security reasons.
     *
     * However, if a non-browser user agent such as mobile hits
     * this endpoint, the returned token should be passed to the
     * subsequent requests in the Authorization header as a Bearer token.
     *
     * @unauthenticated
     *
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
