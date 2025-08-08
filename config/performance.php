<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains performance-related configuration options
    | for the application.
    |
    */

    'cache' => [
        'enabled' => env('PERFORMANCE_CACHE_ENABLED', true),
        'duration' => env('PERFORMANCE_CACHE_DURATION', 300), // 5 minutes
        'tags' => [
            'recipes' => 'recipes_cache',
            'categories' => 'categories_cache',
            'users' => 'users_cache',
        ],
    ],

    'database' => [
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 100), // milliseconds
        'optimize_queries' => env('DB_OPTIMIZE_QUERIES', true),
    ],

    'assets' => [
        'minify' => env('ASSETS_MINIFY', true),
        'version' => env('ASSETS_VERSION', false),
        'cdn_url' => env('ASSETS_CDN_URL', null),
    ],

    'images' => [
        'optimize' => env('IMAGES_OPTIMIZE', true),
        'lazy_loading' => env('IMAGES_LAZY_LOADING', true),
        'webp_support' => env('IMAGES_WEBP_SUPPORT', true),
        'max_width' => env('IMAGES_MAX_WIDTH', 1200),
        'quality' => env('IMAGES_QUALITY', 85),
    ],

    'livewire' => [
        'debounce' => env('LIVEWIRE_DEBOUNCE', 300),
        'optimize_components' => env('LIVEWIRE_OPTIMIZE_COMPONENTS', true),
        'cache_views' => env('LIVEWIRE_CACHE_VIEWS', true),
    ],
]; 