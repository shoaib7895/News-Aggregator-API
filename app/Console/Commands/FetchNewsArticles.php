<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Article;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
        $apis = config('news.apis');

        foreach ($apis as $api) {
            

            // Handle different API structures
            if ($api['source'] === 'newsapi') {
                $randomIndex = rand(0, 2);
                $category = $api['category'][$randomIndex];
                $response = $client->request('GET', $api['url'], [
                    'query' => [
                        'apiKey' => $api['key'],
                        'category' => $category,
                        'country' => 'us',
                    ]
                ]);
    
                $data = json_decode($response->getBody(), true);
                if(isset($data['articles']) && is_array($data['articles'])){
                    foreach ($data['articles'] as $article) {
                        Article::updateOrCreate(
                            ['title' => $article['title']],
                            [
                                'title' => $article['title'],
                                'content' => $article['content'] ?? 'No Content Found!',
                                'source' => $article['source']['name'] ?? 'Unknown',
                                'category' => $category, 
                                'author' => $article['author'] ?? 'Unknown',
                                'published_at' => isset($article['publishedAt']) ? date('Y-m-d', strtotime($article['publishedAt'])) : null,
                            ]
                        );
                    }
                }
            } elseif ($api['source'] === 'guardian') {
                $response = $client->request('GET', $api['url'], [
                    'query' => [
                        'api-key' => $api['key'],
                        'category' => $category,
                        'country' => 'us', 
                    ]
                ]);
                $data = json_decode($response->getBody(), true);
               if(isset($data['response']['results']) && is_array($data['response']['results'])){
                foreach ($data['response']['results'] as $article) {
                    Article::updateOrCreate(
                        ['title' => $article['webTitle']],
                        [
                            'title' => $article['webTitle'],
                            'content' => $article['fields']['bodyText'] ?? 'No Content',
                            'source' => 'The Guardian',
                            'category' => $article['sectionName'] ?? 'General',
                            'author' => $article['tags'][0]['webTitle'] ?? 'Unknown',
                            'published_at' => isset($article['webPublicationDate']) ? date('Y-m-d', strtotime($article['webPublicationDate'])) : null,
                        ]
                    );
                }
               }
            } elseif ($api['source'] === 'nyt') {
                $randomIndex = rand(0, 2);
                $category = $api['category'][$randomIndex];
                $response = $client->request('GET', $api['url'], [
                    'query' => [
                        'api-key' => $api['key'],
                        'q' => $category,
                    ]
                ]);
    
                $data = json_decode($response->getBody(), true);
                if(isset($data['response']['docs']) && is_array($data['response']['docs'])){
                    foreach ($data['response']['docs'] as $article) {
                        Article::updateOrCreate(
                            ['title' => $article['headline']['main']],
                            [
                                'title' => $article['headline']['main'],
                                'content' => $article['snippet'] ?? 'No Content',
                                'source' => 'The New York Times',
                                'category' => $category,
                                'author' => $article['byline']['original'] ?? 'Unknown',
                                'published_at' => isset($article['pub_date']) ? date('Y-m-d', strtotime($article['pub_date'])) : null,
                            ]
                        );
                    }
                }
            }
        }

        $this->info('Articles fetched and stored successfully!');
    }
}
