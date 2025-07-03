<?php

use App\Models\User;

test('verification email can be sent', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('/api/v1/email/verification-notification');

    $response->assertStatus(200);
});

test('verifying mail with invalid token throws error', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson("/api/v1/email/verify/{$user->id}/asdfsfs");

    $response->assertStatus(403)->assertJson(['message' => 'Invalid signature.']);
});
