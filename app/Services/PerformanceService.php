<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /**
     * Cache duration in seconds
     */
    const CACHE_DURATION = 300; // 5 minutes

    /**
     * Get cached statistics
     */
    public function getCachedStats(): array
    {
        return Cache::remember('app_stats', self::CACHE_DURATION, function () {
            return [
                'recipes' => Recipe::where('status', 'approved')->count(),
                'users' => User::count(),
                'categories' => Category::count(),
            ];
        });
    }

    /**
     * Get cached categories with recipe counts
     */
    public function getCachedCategories(int $limit = 6)
    {
        return Cache::remember("categories_with_recipes_{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return Category::where('parent_id', null)
                ->with(['children:id,parent_id,name,slug', 'recipes:id'])
                ->withCount('recipes')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear all application caches
     */
    public function clearAllCaches(): void
    {
        Cache::flush();
        Cache::tags(['recipes', 'categories', 'users'])->flush();
    }

    /**
     * Optimize database queries
     */
    public function optimizeQueries(): void
    {
        // Update statistics in background
        dispatch(function () {
            $this->updateRecipeStats();
        })->afterResponse();
    }

    /**
     * Update recipe statistics
     */
    private function updateRecipeStats(): void
    {
        Recipe::chunk(100, function ($recipes) {
            foreach ($recipes as $recipe) {
                $recipe->updateRatingStats();
                $recipe->updateFavoriteCount();
            }
        });
    }

    /**
     * Get database query statistics
     */
    public function getQueryStats(): array
    {
        if (config('app.debug')) {
            return [
                'queries' => DB::getQueryLog(),
                'query_count' => count(DB::getQueryLog()),
            ];
        }

        return ['message' => 'Query logging disabled in production'];
    }
} 