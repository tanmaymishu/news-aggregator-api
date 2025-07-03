<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Mark the user's e-mail as verified.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(['message' => 'Verification successful!']);
    }

    /**
     * Notify the user by sending verification email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }
}
