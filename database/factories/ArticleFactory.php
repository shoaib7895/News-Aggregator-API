<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'source' => $this->faker->optional()->word,  // Optional source
            'category' => $this->faker->optional()->word,  // Optional category
            'author' => $this->faker->optional()->name,  // Optional author
            'published_at' => $this->faker->optional()->dateTimeThisYear,  // Optional published date
        ];
    }
}
