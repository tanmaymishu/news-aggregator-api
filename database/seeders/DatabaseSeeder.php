<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Preference;
use App\Models\Source;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $preferredSource = 'WSJ';
        $preferredCategory = 'Sport';
        $preferredAuthor = 'Staff Reporter';

        Source::factory()->create(['name' => $preferredSource]);
        Category::factory()->create(['name' => $preferredCategory]);
        Author::factory()->create(['name' => $preferredAuthor]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        Preference::factory()->create([
            'user_id' => $user->id,
            'sources' => [$preferredSource],
            'categories' => [$preferredCategory],
            'authors' => [$preferredAuthor],
        ]);

        // Of total 100 articles, 60 will be personalized for the $user
        Article::factory(40)->create();
        Article::factory(20)->create(['source' => $preferredSource]);
        Article::factory(20)->create(['category' => $preferredCategory]);
        Article::factory(20)->create(['author' => $preferredAuthor]);

        // After seeding, login with [test@example.com, password] and hit
        // the /api/v1/own-articles endpoint. Each item in the returned result
        // will have at least a source value of 'WSJ', or category value of 'Sport'
        // or author value of 'Staff Reporter'.
    }
}
