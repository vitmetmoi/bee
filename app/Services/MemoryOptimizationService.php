<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MemoryOptimizationService
{
    /**
     * Configure optimal memory settings for the application
     */
    public static function configure(): void
    {
        if (function_exists('ini_set')) {
            // Set memory limit to 256M (double the current limit)
            ini_set('memory_limit', '256M');
            
            // Set reasonable execution time
            ini_set('max_execution_time', '60');
            
            // Enable garbage collection
            if (function_exists('gc_enable')) {
                gc_enable();
            }
        }
    }

    /**
     * Monitor memory usage and log warnings
     */
    public static function monitor(string $context = 'general'): array
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        $memoryLimit = self::getMemoryLimitInBytes();
        
        $stats = [
            'current' => $memoryUsage,
            'peak' => $peakMemory,
            'limit' => $memoryLimit,
            'usage_percentage' => round(($peakMemory / $memoryLimit) * 100, 2),
            'formatted' => [
                'current' => self::formatBytes($memoryUsage),
                'peak' => self::formatBytes($peakMemory),
                'limit' => self::formatBytes($memoryLimit)
            ]
        ];
        
        // Log warning if memory usage is above 80%
        if ($stats['usage_percentage'] > 80) {
            Log::warning('High memory usage detected', [
                'context' => $context,
                'usage_percentage' => $stats['usage_percentage'],
                'peak_memory' => $stats['formatted']['peak'],
                'memory_limit' => $stats['formatted']['limit']
            ]);
        }
        
        return $stats;
    }

    /**
     * Force garbage collection
     */
    public static function cleanUp(): void
    {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * Get memory limit in bytes
     */
    private static function getMemoryLimitInBytes(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit == -1) {
            return PHP_INT_MAX;
        }
        
        $unit = strtoupper(substr($memoryLimit, -1));
        $value = (int) substr($memoryLimit, 0, -1);
        
        switch ($unit) {
            case 'G':
                return $value * 1024 * 1024 * 1024;
            case 'M':
                return $value * 1024 * 1024;
            case 'K':
                return $value * 1024;
            default:
                return (int) $memoryLimit;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if memory optimization is needed
     */
    public static function isOptimizationNeeded(): bool
    {
        $stats = self::monitor();
        return $stats['usage_percentage'] > 70;
    }

    /**
     * Get memory optimization recommendations
     */
    public static function getRecommendations(): array
    {
        $stats = self::monitor();
        $recommendations = [];
        
        if ($stats['usage_percentage'] > 90) {
            $recommendations[] = 'Critical: Memory usage above 90%. Consider increasing memory_limit or optimizing queries.';
        } elseif ($stats['usage_percentage'] > 80) {
            $recommendations[] = 'Warning: Memory usage above 80%. Monitor for potential memory leaks.';
        }
        
        if (ini_get('memory_limit') === '128M') {
            $recommendations[] = 'Consider increasing memory_limit from 128M to 256M or 512M.';
        }
        
        if (!function_exists('gc_enable') || !gc_enabled()) {
            $recommendations[] = 'Enable garbage collection for better memory management.';
        }
        
        return $recommendations;
    }
}
