<?php

return [
    'apis' => [
        [
            'key' => env('NEWS_API_KEY'),
            'url' => env('NEWS_API_URL'),
            'source' => 'newsapi',
            'category' => ['bussiness','sport','entertainment']
        ],
        [
            'key' => env('GUARDIAN_API_KEY'),
            'url' => env('GUARDIAN_API_URL'),
            'source' => 'guardian'
        ],
        [
            'key' => env('NYT_API_KEY'),
            'url' => env('NYT_API_URL'),
            'source' => 'nyt',
            'category' => ['bussiness','sport','election']
        ],
    ],
];