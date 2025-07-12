<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

final class EmailVerificationController
{
    /**
     * Mark the user's e-mail as verified.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(['message' => 'Verification successful!']);
    }

    /**
     * Notify the user by sending verification email.
     *
     * If the APP_FRONTEND_URL env variable is present,
     * a link pointing to the frontend (e.g. nextjs) application will be sent.
     * Otherwise, A link pointing to the backend (/api/v1/...) will be sent.
     * In both cases, the link must be copied and pasted to a user-agent (e.g. Postman/Browser)
     * where the user is already logged-in, either with an active session, or a bearer token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }
}
