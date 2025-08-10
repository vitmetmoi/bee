<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemoryOptimization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set memory limit and execution time based on config
        $memoryLimit = config('app.memory_limit', '256M');
        $maxExecutionTime = config('app.max_execution_time', 60);
        
        if (function_exists('ini_set')) {
            ini_set('memory_limit', $memoryLimit);
            ini_set('max_execution_time', $maxExecutionTime);
        }
        
        // Log memory usage before request
        $initialMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        // Log memory usage after request in development
        if (config('app.debug')) {
            $finalMemory = memory_get_usage(true);
            $peakMemory = memory_get_peak_usage(true);
            
            \Log::info('Memory Usage', [
                'initial' => $this->formatBytes($initialMemory),
                'final' => $this->formatBytes($finalMemory),
                'peak' => $this->formatBytes($peakMemory),
                'used' => $this->formatBytes($finalMemory - $initialMemory),
                'url' => $request->url()
            ]);
            
            // Warning if memory usage is high
            if ($peakMemory > (128 * 1024 * 1024)) { // 128MB
                \Log::warning('High memory usage detected', [
                    'peak_memory' => $this->formatBytes($peakMemory),
                    'url' => $request->url()
                ]);
            }
        }
        
        return $response;
    }
    
    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
