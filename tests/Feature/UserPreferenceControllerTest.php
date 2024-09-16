<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_show_user_preferences()
    {
        // Create a user and assign preferences
        $user = User::factory()->create();
        UserPreference::factory()->create(['user_id' => $user->id]);

        // Act as the created user
        Sanctum::actingAs($user);

        // Send GET request
        $response = $this->getJson('/api/preferences');

        // Assert response
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'preferred_sources',
                     'preferred_categories',
                     'preferred_authors',
                 ]);
    }

    /** @test */
    public function it_can_update_user_preferences()
    {
        // Create a user
        $user = User::factory()->create();

        // Define preferences
        $preferences = [
            'preferred_sources' => ['Source1', 'Source2'],
            'preferred_categories' => ['Category1'],
            'preferred_authors' => ['Author1'],
        ];

        // Act as the created user
        Sanctum::actingAs($user);

        // Send POST request
        $response = $this->postJson('/api/preferences', $preferences);

        // Assert response
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Preferences updated successfully']);
    }

    /** @test */
    public function it_can_get_personalized_feed()
    {
        // Create a user and assign preferences
        $user = User::factory()->create();
        $pre = UserPreference::factory()->create(['user_id' => $user->id, 'preferred_sources' => ['Source1'],'preferred_categories' => ['Business'],'preferred_authors'=> ['ABC']]);

        // Create an article that matches user preferences
        $article = Article::factory()->create(['source' => 'Source1','category' => 'Business' , 'author' => 'ABC']);
    
        // Act as the created user
        Sanctum::actingAs($user);
        // Send GET request
        $response = $this->getJson('/api/personalized-feed');
        
        // Assert response
        $response->assertStatus(200)
        ->assertJson([
            'data' => [
                ['id' => $article->id]
            ]
        ]);
    }
}
