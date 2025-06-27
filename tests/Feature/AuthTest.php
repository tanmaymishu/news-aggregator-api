<?php

use App\Models\User;
use Illuminate\Support\Arr;

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
