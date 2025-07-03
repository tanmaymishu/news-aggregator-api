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
    public function store(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(['message' => 'Verification successful!']);
    }

    /**
     * Notify the user by sending verification email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }
}
