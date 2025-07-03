<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->paragraph,
            'web_url' => $this->faker->unique()->url,
            'featured_image_url' => $this->faker->unique()->imageUrl,
            'source' => $this->faker->randomElement(['BBC News', 'News Api', 'The Guardian', 'The New York Times']),
            'author' => $this->faker->name,
            'category' => $this->faker->word,
            'published_at' => $this->faker->date,
        ];
    }
}
