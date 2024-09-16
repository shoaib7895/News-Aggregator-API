<?php

namespace Database\Factories;

use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'preferred_sources' => $this->faker->randomElements([
                'Source A', 'Source B', 'Source C', 'Source D'
            ], rand(1, 3)),  // Randomly selects 1 to 3 sources
            'preferred_categories' => $this->faker->randomElements([
                'Technology', 'Health', 'Science', 'Business'
            ], rand(1, 3)),  // Randomly selects 1 to 3 categories
            'preferred_authors' => $this->faker->randomElements([
                'Author A', 'Author B', 'Author C', 'Author D'
            ], rand(1, 3)),  // Randomly selects 1 to 3 authors
        ];
    }
}
