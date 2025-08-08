<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VietnamProvinceService
{
    protected $baseUrl = 'https://provinces.open-api.vn/api/v1';
    protected $cacheTime = 86400; // 24 giờ

    /**
     * Lấy danh sách tất cả tỉnh thành
     */
    public function getAllProvinces()
    {
        return Cache::remember('vietnam_provinces', $this->cacheTime, function () {
            try {
                Log::info('Bắt đầu gọi API tỉnh thành: ' . $this->baseUrl);
                
                $response = Http::timeout(30)
                    ->withoutVerifying() // Disable SSL verification
                    ->withOptions([
                        'verify' => false,
                        'curl' => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ]
                    ])
                    ->get($this->baseUrl . '?depth=2');
                
                // Log response details
                Log::info('API Response Status: ' . $response);
                
                if ($response->successful()) {
                    $provinces = $response->json();
                    
                    // Log response body for debugging
                    Log::info('API Response Body (first 500 chars): ' . substr(json_encode($provinces), 0, 500));
                    Log::info('Đã lấy thành công ' . count($provinces) . ' tỉnh thành từ API');
                    
                    // Validate response structure
                    if (empty($provinces)) {
                        Log::warning('API trả về mảng rỗng');
                    } else {
                        Log::info('Cấu trúc dữ liệu mẫu: ' . json_encode(array_slice($provinces, 0, 1)));
                    }
                    
                    return $provinces;
                } else {
                    Log::error('Lỗi khi gọi API tỉnh thành: ' . $response->status());
                    Log::error('Response Body: ' . $response->body());
                    Log::error('Response Headers: ' . json_encode($response->headers()));
                    return [];
                }
            } catch (\Exception $e) {
                Log::error('Exception khi gọi API tỉnh thành: ' . $e->getMessage());
                Log::error('Exception Stack Trace: ' . $e->getTraceAsString());
                return [];
            }
        });
    }

    /**
     * Lấy thông tin chi tiết của một tỉnh theo code
     */
    public function getProvinceByCode($code)
    {
        return Cache::remember("vietnam_province_{$code}", $this->cacheTime, function () use ($code) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->withOptions([
                        'verify' => false,
                        'curl' => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ]
                    ])
                    ->get($this->baseUrl . '/p/' . $code);
                
                if ($response->successful()) {
                    $province = $response->json();
                    Log::info("Đã lấy thông tin tỉnh: {$province['name']} (code: {$code})");
                    return $province;
                } else {
                    Log::error("Lỗi khi lấy thông tin tỉnh code {$code}: " . $response->status());
                    return null;
                }
            } catch (\Exception $e) {
                Log::error("Exception khi lấy thông tin tỉnh code {$code}: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Lấy danh sách quận/huyện của một tỉnh
     */
    public function getDistrictsByProvinceCode($provinceCode)
    {
        return Cache::remember("vietnam_districts_{$provinceCode}", $this->cacheTime, function () use ($provinceCode) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->withOptions([
                        'verify' => false,
                        'curl' => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ]
                    ])
                    ->get($this->baseUrl . '/p/' . $provinceCode . '?depth=2');
                
                if ($response->successful()) {
                    $province = $response->json();
                    $districts = $province['districts'] ?? [];
                    Log::info("Đã lấy thành công " . count($districts) . " quận/huyện cho tỉnh code {$provinceCode}");
                    return $districts;
                } else {
                    Log::error("Lỗi khi lấy quận/huyện tỉnh code {$provinceCode}: " . $response->status());
                    return [];
                }
            } catch (\Exception $e) {
                Log::error("Exception khi lấy quận/huyện tỉnh code {$provinceCode}: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Lấy danh sách xã/phường của một quận/huyện
     */
    public function getWardsByDistrictCode($districtCode)
    {
        return Cache::remember("vietnam_wards_{$districtCode}", $this->cacheTime, function () use ($districtCode) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->withOptions([
                        'verify' => false,
                        'curl' => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ]
                    ])
                    ->get($this->baseUrl . '/d/' . $districtCode . '?depth=2');
                
                if ($response->successful()) {
                    $district = $response->json();
                    $wards = $district['wards'] ?? [];
                    Log::info("Đã lấy thành công " . count($wards) . " xã/phường cho quận/huyện code {$districtCode}");
                    return $wards;
                } else {
                    Log::error("Lỗi khi lấy xã/phường quận/huyện code {$districtCode}: " . $response->status());
                    return [];
                }
            } catch (\Exception $e) {
                Log::error("Exception khi lấy xã/phường quận/huyện code {$districtCode}: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Tìm tỉnh theo tên (tìm kiếm mờ)
     */
    public function searchProvincesByName($name)
    {
        $provinces = $this->getAllProvinces();
        
        return collect($provinces)->filter(function ($province) use ($name) {
            return stripos($province['name'], $name) !== false ||
                   stripos($province['codename'], $name) !== false;
        })->values()->all();
    }

    /**
     * Lấy tỉnh theo tên chính xác
     */
    public function getProvinceByName($name)
    {
        $provinces = $this->getAllProvinces();
        
        return collect($provinces)->first(function ($province) use ($name) {
            return strtolower($province['name']) === strtolower($name);
        });
    }

    /**
     * Xóa cache
     */
    public function clearCache()
    {
        Cache::forget('vietnam_provinces');
        
        // Xóa cache của các tỉnh cụ thể
        $provinces = $this->getAllProvinces();
        foreach ($provinces as $province) {
            Cache::forget("vietnam_province_{$province['code']}");
            Cache::forget("vietnam_districts_{$province['code']}");
        }
        
        Log::info('Đã xóa cache tỉnh thành');
    }

    /**
     * Lấy dữ liệu tỉnh thành mà không sử dụng cache (cho debug)
     */
    public function getAllProvincesWithoutCache()
    {
        try {
            Log::info('Bắt đầu gọi API tỉnh thành (không cache): ' . $this->baseUrl);
            
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->get($this->baseUrl);
            
            // Log response details
            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Headers: ' . json_encode($response->headers()));
            
            if ($response->successful()) {
                $provinces = $response->json();
                
                // Log response body for debugging
                Log::info('API Response Body (first 500 chars): ' . substr(json_encode($provinces), 0, 500));
                Log::info('Đã lấy thành công ' . count($provinces) . ' tỉnh thành từ API (không cache)');
                
                // Validate response structure
                if (empty($provinces)) {
                    Log::warning('API trả về mảng rỗng');
                } else {
                    Log::info('Cấu trúc dữ liệu mẫu: ' . json_encode(array_slice($provinces, 0, 1)));
                }
                
                return $provinces;
            } else {
                Log::error('Lỗi khi gọi API tỉnh thành: ' . $response->status());
                Log::error('Response Body: ' . $response->body());
                Log::error('Response Headers: ' . json_encode($response->headers()));
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Exception khi gọi API tỉnh thành: ' . $e->getMessage());
            Log::error('Exception Stack Trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Kiểm tra kết nối API
     */
    public function testConnection()
    {
        try {
            Log::info('Testing API connection to: ' . $this->baseUrl . '/p/');
            
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->get($this->baseUrl . '/p/');
            
            Log::info('API test response status: ' . $response->status());
            Log::info('API test response body: ' . substr($response->body(), 0, 200));
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối API tỉnh thành: ' . $e->getMessage());
            Log::error('Exception details: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Lấy thống kê tỉnh thành
     */
    public function getStats()
    {
        $provinces = $this->getAllProvinces();
        
        return [
            'total_provinces' => count($provinces),
            'last_updated' => now()->format('Y-m-d H:i:s'),
            'api_status' => $this->testConnection() ? 'online' : 'offline',
            'cache_status' => Cache::has('vietnam_provinces') ? 'cached' : 'not_cached'
        ];
    }
} 