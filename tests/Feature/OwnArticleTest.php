<?php

use App\Models\Article;
use App\Models\User;

describe('personalized articles', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    test('can be retrieved by authenticated user only', function () {
        $articles = Article::factory(10)->create();

        $response = $this->getJson('/api/v1/own-articles');

        $response->assertUnauthorized();

        $response = $this->actingAs($this->user)->getJson('/api/v1/own-articles');

        $response->assertStatus(200)
            ->assertJsonFragment(['data' => $articles->toArray()]);
    });

    test('can be filtered by source', function () {
        Article::factory(5)->create();
        $articles = Article::factory(5)->create(['source' => 'NewsApi']);

        $response = $this->actingAs($this->user)->getJson('/api/v1/own-articles?source=NewsApi');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['data' => $articles->toArray()]);
    });

    test('can be filtered by category', function () {
        $user = User::factory()->create();
        Article::factory(5)->create();
        $articles = Article::factory(5)->create(['category' => 'Sport']);

        $response = $this->actingAs($this->user)->getJson('/api/v1/own-articles?category=Sport');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['data' => $articles->toArray()]);
    });

    test('can be filtered by author', function () {
        Article::factory(5)->create();
        $articles = Article::factory(5)->create(['author' => 'Tanmay']);

        $response = $this->actingAs($this->user)->getJson('/api/v1/own-articles?author=Tanmay');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['data' => $articles->toArray()]);
    });
});
