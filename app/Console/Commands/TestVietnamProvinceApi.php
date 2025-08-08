<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VietnamProvinceService;

class TestVietnamProvinceApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:vietnam-province-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to Vietnam Province API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Vietnam Province API connection...');
        
        $service = new VietnamProvinceService();
        
        // Test basic connection
        $this->info('1. Testing basic connection...');
        if ($service->testConnection()) {
            $this->info('✅ Connection successful!');
        } else {
            $this->error('❌ Connection failed!');
            return 1;
        }
        
        // Test getting all provinces
        $this->info('2. Testing get all provinces...');
        $provinces = $service->getAllProvinces();
        
        if (!empty($provinces)) {
            $this->info("✅ Successfully retrieved " . count($provinces) . " provinces");
            
            // Show first few provinces
            $this->info('First 3 provinces:');
            foreach (array_slice($provinces, 0, 3) as $province) {
                $this->line("  - {$province['name']} (Code: {$province['code']})");
            }
        } else {
            $this->error('❌ Failed to retrieve provinces');
            return 1;
        }
        
        // Test getting a specific province
        $this->info('3. Testing get specific province (Hà Nội)...');
        $hanoi = $service->getProvinceByName('Thành phố Hà Nội');
        
        if ($hanoi) {
            $this->info("✅ Successfully retrieved Hà Nội province");
            $this->line("  - Name: {$hanoi['name']}");
            $this->line("  - Code: {$hanoi['code']}");
            $this->line("  - Districts: " . count($hanoi['districts'] ?? []));
        } else {
            $this->error('❌ Failed to retrieve Hà Nội province');
        }
        
        // Test getting districts
        $this->info('4. Testing get districts for Hà Nội...');
        $districts = $service->getDistrictsByProvinceCode(1); // Hà Nội code
        
        if (!empty($districts)) {
            $this->info("✅ Successfully retrieved " . count($districts) . " districts for Hà Nội");
            $this->line('First 3 districts:');
            foreach (array_slice($districts, 0, 3) as $district) {
                $this->line("  - {$district['name']} (Code: {$district['code']})");
            }
        } else {
            $this->error('❌ Failed to retrieve districts');
        }
        
        $this->info('🎉 All tests completed successfully!');
        return 0;
    }
} 