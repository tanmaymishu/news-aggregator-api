<?php

use App\Models\User;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

test('a user can register', function () {
    $response = $this->postJson(route('register'));
    $response->assertStatus(422);

    $attrs = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson(route('register'), $attrs);

    $response->assertCreated()
        ->assertJsonFragment(Arr::only($attrs, ['name', 'email']));

});

test('a user can login', function () {
    $user = User::factory()->createOne();

    $response = $this->postJson(route('login'));
    $response->assertStatus(422);

    $response = $this->postJson(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertCreated()
        ->assertJsonFragment(Arr::only($user->toArray(), ['name', 'email']));

});

test('a user can logout', function () {
    $user = User::factory()->createOne();
    $user->createToken('test');

    $response = $this->deleteJson(route('logout'));
    $response->assertStatus(401);

    Sanctum::actingAs($user);
    $response = $this->deleteJson(route('logout'));

    $response->assertNoContent();
});

test('a user can ask for password reset email', function () {
    $user = User::factory()->createOne();

    $response = $this->postJson(route('password.email'));
    $response->assertStatus(422);

    $response = $this->postJson(route('password.email'), [
        'email' => $user->email,
    ]);

    $response->assertOk();
});

test('a user can reset password', function () {
    $user = User::factory()->createOne();

    $response = $this->postJson(route('password.update'));
    $response->assertUnprocessable();

    $response = $this->postJson(route('password.update'), [
        'token' => \Illuminate\Support\Facades\Password::createToken($user),
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertOk();
});
