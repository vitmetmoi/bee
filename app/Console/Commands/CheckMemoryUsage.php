<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MemoryOptimizationService;

class CheckMemoryUsage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'memory:check {--detailed : Show detailed memory information}';

    /**
     * The console command description.
     */
    protected $description = 'Check current memory usage and optimization status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Memory Usage Check');
        $this->info('=====================');

        // Get current memory stats
        $stats = MemoryOptimizationService::monitor('console-check');
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Current Memory Usage', $stats['formatted']['current']],
                ['Peak Memory Usage', $stats['formatted']['peak']],
                ['Memory Limit', $stats['formatted']['limit']],
                ['Usage Percentage', $stats['usage_percentage'] . '%'],
                ['PHP Version', PHP_VERSION],
                ['Max Execution Time', ini_get('max_execution_time') . 's'],
            ]
        );

        // Show status based on usage
        if ($stats['usage_percentage'] > 90) {
            $this->error('❌ Critical: Memory usage is very high!');
        } elseif ($stats['usage_percentage'] > 80) {
            $this->warn('⚠️  Warning: Memory usage is high');
        } elseif ($stats['usage_percentage'] > 70) {
            $this->comment('⚡ Caution: Monitor memory usage');
        } else {
            $this->info('✅ Good: Memory usage is normal');
        }

        // Show recommendations
        $recommendations = MemoryOptimizationService::getRecommendations();
        if (!empty($recommendations)) {
            $this->newLine();
            $this->info('💡 Recommendations:');
            foreach ($recommendations as $recommendation) {
                $this->line('• ' . $recommendation);
            }
        }

        // Show detailed info if requested
        if ($this->option('detailed')) {
            $this->newLine();
            $this->info('🔧 Detailed Information:');
            $this->line('Memory Limit (bytes): ' . number_format($stats['limit']));
            $this->line('Current Usage (bytes): ' . number_format($stats['current']));
            $this->line('Peak Usage (bytes): ' . number_format($stats['peak']));
            $this->line('Available Memory: ' . number_format($stats['limit'] - $stats['peak']) . ' bytes');
            $this->line('Garbage Collection Enabled: ' . (gc_enabled() ? 'Yes' : 'No'));
        }

        return Command::SUCCESS;
    }
}
