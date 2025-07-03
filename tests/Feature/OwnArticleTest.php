<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
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

    test('can be retrieved based on the saved preferences', function () {
        // These should not be in the response
        Article::factory(2)->create();

        $preferredSource = 'WSJ';
        $preferredCategory = 'Sport';
        $preferredAuthor = 'Staff Reporter';

        Source::factory()->create(['name' => $preferredSource]);
        Category::factory()->create(['name' => $preferredCategory]);
        Author::factory()->create(['name' => $preferredAuthor]);

        // These should be in the response (Total: 6, not 8)
        Article::factory(2)->create(['category' => $preferredCategory]);
        Article::factory(2)->create(['source' => $preferredSource]);
        Article::factory(2)->create(['author' => $preferredAuthor]);

        $this->actingAs($this->user)->patchJson('/api/v1/preferences', [
            'sources' => [$preferredSource],
            'categories' => [$preferredCategory],
            'authors' => [$preferredAuthor],
        ])->assertOk();

        $this->actingAs($this->user)->getJson('/api/v1/own-articles')
            ->assertOk()
            ->assertJsonCount(6, 'data');
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
