<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;

test('sources can be fetched', function () {
    $sources = Source::factory(10)->create();

    $this->assertDatabaseCount(Source::class, 10);

    $response = $this->getJson('/api/v1/sources');

    $response->assertStatus(200);
});

test('authors can be fetched', function () {
    $authors = Author::factory(10)->create();

    $this->assertDatabaseCount(Author::class, 10);

    $response = $this->getJson('/api/v1/authors');

    $response->assertStatus(200);
});

test('categories can be fetched', function () {
    $categories = Category::factory(10)->create();

    $this->assertDatabaseCount(Category::class, 10);

    $response = $this->getJson('/api/v1/categories');

    $response->assertStatus(200);
});
