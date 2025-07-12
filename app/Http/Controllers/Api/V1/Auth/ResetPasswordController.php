<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ResetPasswordController
{
    /**
     * Show the password reset form.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    public function show(Request $request, string $token)
    {
        if (empty(config('app.frontend_url'))) {
            return view('auth.reset-password', [
                'token' => $token,
                'email' => $request->query('email'),
            ]);
        }

        return redirect(config('app.frontend_url').'/reset-password?token='.$token.'&email='.$request->query('email'));
    }

    /**
     * Reset the password.
     *
     * @unauthenticated
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages(['message' => __($status)]);
    }
}
