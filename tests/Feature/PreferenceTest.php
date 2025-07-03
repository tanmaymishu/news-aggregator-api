<?php

use App\Models\Source;
use App\Models\User;

test('current preference of a logged-in user can be retrieved', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->getJson('/api/v1/preferences');

    $response->assertOk();
});

test('current preference of a logged-in user can be updated', function () {
    $user = User::factory()->create();
    Source::factory()->create(['name' => 'BBC News']);
    $response = $this->actingAs($user)
        ->patchJson('/api/v1/preferences', [
            'sources' => ['BBC News']
        ]);

    $response->assertOk();
});
