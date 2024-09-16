<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database or create test data if necessary
        $this->seed(); // Or create specific data
    }

    /**
     * Test retrieving a list of articles with authentication.
     *
     * @return void
     */
    public function testGetListOfArticles()
    {
        // Create and authenticate a test user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create test articles
        Article::factory()->count(5)->create();

        $response = $this->get('/api/articles');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'current_page',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'content',
                             'published_at',
                             'category',
                             'source'
                         ]
                     ]
                 ]);
    }

    /**
     * Test retrieving a specific article by ID with authentication.
     *
     * @return void
     */
    public function testGetSpecificArticle()
    {
        // Create and authenticate a test user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create a test article
        $article = Article::factory()->create();

        $response = $this->get("/api/articles/{$article->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'id',
                     'title',
                     'content',
                     'published_at',
                     'category',
                     'source'
                 ]);
    }

    /**
     * Test retrieving a non-existent article with authentication.
     *
     * @return void
     */
    public function testGetNonExistentArticle()
    {
        // Create and authenticate a test user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $nonExistentId = 9999; // Assuming this ID does not exist in the database

        $response = $this->get("/api/articles/{$nonExistentId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
