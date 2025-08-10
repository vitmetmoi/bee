<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Rating;
use App\Models\Post;
use App\Models\Recipe;
use App\Policies\RatingPolicy;
use App\Policies\PostPolicy;
use App\Policies\RecipePolicy;
use App\Observers\PostObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Memory optimization settings
        if (function_exists('ini_set')) {
            ini_set('memory_limit', config('app.memory_limit', '256M'));
            
            // Only set execution time limit for web requests, not CLI commands
            if (php_sapi_name() !== 'cli') {
                ini_set('max_execution_time', config('app.max_execution_time', 60));
            }
        }
        
        // Set application locale
        App::setLocale('vi');

        // Register policies
        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        
        Gate::policy(Recipe::class, RecipePolicy::class);

        // Register observers
        Post::observe(PostObserver::class);
        
        // Enable query optimization
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // Log slow queries (>1 second)
                    \Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }
    }
}
