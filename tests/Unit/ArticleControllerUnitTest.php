<?php

namespace Tests\Unit\Controllers\API;

use App\Http\Controllers\API\ArticleController;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery;

class ArticleControllerUnitTest extends TestCase
{
    public function testIndexMethodReturnsArticlesFromCache()
    {
        // Create a mock request with parameters
        $request = new Request(['keyword' => 'test']);

        // Mock the Cache facade
        Cache::shouldReceive('remember')
            ->once()
            ->with(Mockery::type('string'), 60, Mockery::type('Closure'))
            ->andReturn(collect(['article1', 'article2']));  // Mocked article data

        // Create the controller instance
        $controller = new ArticleController();

        // Call the index method
        $response = $controller->index($request);

        // Convert response content to array
        $responseData = json_decode($response->getContent(), true);

        // Assert the response status code
        $this->assertEquals(200, $response->getStatusCode());

        // Assert the response contains the expected articles
        $this->assertEquals(['article1', 'article2'], $responseData);
    }

    public function testShowMethodReturnsArticleFromCache()
    {
        // Mock the Cache facade to return a specific article
        Cache::shouldReceive('remember')
            ->once()
            ->with('article_1', 60, Mockery::type('Closure'))
            ->andReturn(['id' => 1, 'title' => 'Test Article']);

        // Create the controller instance
        $controller = new ArticleController();

        // Call the show method
        $response = $controller->show(1);

        // Convert response content to array
        $responseData = json_decode($response->getContent(), true);

        // Assert the response status code
        $this->assertEquals(200, $response->getStatusCode());

        // Assert the response contains the expected article
        $this->assertEquals(['id' => 1, 'title' => 'Test Article'], $responseData);
    }
}
