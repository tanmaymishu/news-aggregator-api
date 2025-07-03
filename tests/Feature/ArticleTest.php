<?php

use App\Models\Article;

test('public articles can be retrieved', function () {
    $articles = Article::factory(10)->create();

    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200)
        ->assertJsonFragment(['data' => $articles->toArray()]);
});

test('public articles can be filtered by source', function () {
    Article::factory(5)->create();
    $articles = Article::factory(5)->create(['source' => 'NewsApi']);

    $response = $this->getJson('/api/v1/articles?source=NewsApi');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonFragment(['data' => $articles->toArray()]);
});

test('public articles can be filtered by category', function () {
    Article::factory(5)->create();
    $articles = Article::factory(5)->create(['category' => 'Sport']);

    $response = $this->getJson('/api/v1/articles?category=Sport');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonFragment(['data' => $articles->toArray()]);
});

test('public articles can be filtered by author', function () {
    Article::factory(5)->create();
    $articles = Article::factory(5)->create(['author' => 'Tanmay']);

    $response = $this->getJson('/api/v1/articles?author=Tanmay');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonFragment(['data' => $articles->toArray()]);
});
