<div class="py-12 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50" 
     x-data="weatherSlideshow()" 
     @keydown.window="handleKeyboard($event)" 
     tabindex="0">
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Container with White Background -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-8 py-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold mb-2">
                        🌤️ Món Ăn Phù Hợp Với Thời Tiết
                    </h2>
                    <p class="text-lg opacity-90 max-w-2xl mx-auto">
                        Khám phá những món ăn ngon được đề xuất theo thời tiết hiện tại
                    </p>
                </div>
                
                @if($nearestCity)
                    <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3 max-w-md mx-auto">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">
                                Đã tự động chọn <strong>{{ $nearestCity->name }}</strong> (thành phố gần nhất)
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3 max-w-md mx-auto">
                        <div class="text-center space-y-3">
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button wire:click="getUserLocationFromBrowser"
                                        class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Lấy vị trí của tôi
                                </button>
                                <button wire:click="randomCity"
                                        class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                    </svg>
                                    Chọn ngẫu nhiên
                                </button>
                            </div>
                            <p class="text-xs opacity-75">Click để lấy vị trí hoặc chọn thành phố ngẫu nhiên</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="p-8">
                @if($loading)
                    <!-- Loading State -->
                    <div class="text-center py-12">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-orange-600 transition ease-in-out duration-150">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang tải đề xuất món ăn...
                        </div>
                    </div>
                @elseif($error)
                    <!-- Error State -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                        <div class="flex justify-center mb-4">
                            <svg class="h-12 w-12 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-red-800 mb-2">Không thể tải dữ liệu</h3>
                        <p class="text-red-600">{{ $error }}</p>
                    </div>
                @elseif($suggestions->count() > 0)
                    <!-- Weather Info Card -->
                    <div class="bg-white rounded-xl p-6 mb-8 border border-gray-100 shadow-sm">
                        <div class="flex flex-col lg:flex-row items-center justify-between">
                            <div class="flex items-center mb-4 lg:mb-0">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mr-4 shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <!-- City Selector -->
                                    <div class="relative">
                                        <select wire:model.live="selectedCity" 
                                                class="appearance-none bg-transparent border-none text-xl font-semibold text-gray-900 focus:outline-none focus:ring-0 cursor-pointer pr-6">
                                            @foreach(\App\Models\VietnamCity::active()->ordered()->get()->groupBy('region') as $region => $cities)
                                                <optgroup label="{{ $region }}">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->code }}" {{ $selectedCity === $city->code ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $weatherData['weather_description'] ?? 'Không có mô tả' }}</p>
                                    @if($nearestCity && $selectedCity === $nearestCity->code)
                                        <p class="text-xs text-green-600 mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Đã tự động chọn {{ $nearestCity->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-8">
                                <div class="text-center">
                                    <p class="text-3xl font-bold {{ $this->getTemperatureColor($weatherData['temperature']) }}">
                                        {{ number_format($weatherData['temperature'], 1) }}°C
                                    </p>
                                    <p class="text-sm text-gray-600">Nhiệt độ</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-blue-500">{{ $weatherData['humidity'] }}%</p>
                                    <p class="text-sm text-gray-600">Độ ẩm</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-800 bg-gradient-to-r from-orange-100 to-red-100 px-4 py-2 rounded-full shadow-sm border border-orange-200">
                                        {{ $this->getSuggestionReason() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slideshow Container -->
                    <div class="relative bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <!-- Slides -->
                        <div class="relative h-96" wire:key="slideshow-container">
                            @foreach($suggestions as $index => $recipe)
                                <div class="absolute inset-0 transition-all duration-500 ease-in-out {{ $index === $currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                     wire:key="slide-{{ $index }}">
                                    <div class="flex h-full">
                                        <!-- Recipe Image -->
                                        <div class="w-1/2 relative">
                                            @if($recipe->featured_image)
                                                <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                                     alt="{{ $recipe->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                                                    <svg class="w-24 h-24 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="absolute top-4 left-4">
                                                <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                                    Món {{ $index + 1 }}/{{ $suggestions->count() }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Recipe Info -->
                                        <div class="w-1/2 p-8 flex flex-col justify-center">
                                            <div class="mb-4">
                                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $recipe->title }}</h3>
                                                <p class="text-gray-600 line-clamp-3">{{ $recipe->summary }}</p>
                                            </div>

                                            <div class="flex items-center space-x-4 mb-4 text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $recipe->cooking_time }} phút
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                    {{ number_format($recipe->average_rating, 1) }}
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap gap-2 mb-6">
                                                @foreach($recipe->categories->take(3) as $category)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>

                                            <div class="flex space-x-3">
                                                <a href="{{ route('recipes.show', $recipe->slug) }}" 
                                                   class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-4 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all duration-200 text-center font-medium shadow-lg">
                                                    Xem chi tiết
                                                </a>
                                                <button class="bg-gray-100 text-gray-700 p-3 rounded-lg hover:bg-gray-200 transition-colors shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation Arrows -->
                        @if($suggestions->count() > 1)
                            <button wire:click="previousSlide" 
                                    wire:loading.attr="disabled"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <button wire:click="nextSlide" 
                                    wire:loading.attr="disabled"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed z-20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif

                        <!-- Dots Indicator -->
                        @if($suggestions->count() > 1)
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
                                @foreach($suggestions as $index => $recipe)
                                    <button wire:click="goToSlide({{ $index }})" 
                                            wire:loading.attr="disabled"
                                            wire:key="dot-{{ $index }}"
                                            class="w-3 h-3 rounded-full transition-all duration-200 disabled:cursor-not-allowed {{ $index === $currentSlide ? 'bg-orange-500' : 'bg-gray-300 hover:bg-gray-400' }}"
                                            aria-label="Chuyển đến slide {{ $index + 1 }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <!-- Auto-play Toggle -->
                        @if($suggestions->count() > 1)
                            <button wire:click="toggleAutoPlay" 
                                    wire:loading.attr="disabled"
                                    class="absolute top-4 right-4 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-2 rounded-full shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed z-20"
                                    title="{{ $autoPlay ? 'Tạm dừng tự động chuyển slide' : 'Bật tự động chuyển slide' }}">
                                @if($autoPlay)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </button>
                        @endif
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-8">
                        <a href="{{ route('weather.suggestions') }}" 
                           class="inline-flex items-center px-6 py-3 border-2 border-orange-500 text-base font-medium rounded-lg text-orange-600 bg-white hover:bg-orange-50 transition-all duration-200 hover:shadow-lg">
                            Xem tất cả đề xuất theo thời tiết
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <!-- No Suggestions State -->
                    <div class="bg-gray-50 rounded-xl p-12 text-center border border-gray-200">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Không có đề xuất</h3>
                        <p class="text-gray-600">Hiện tại chưa có món ăn phù hợp với thời tiết này.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modal thông báo vị trí -->
    <div id="location-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Chia sẻ vị trí</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Bạn có muốn chia sẻ vị trí hiện tại để nhận đề xuất món ăn phù hợp với thời tiết không?
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <button id="location-yes" class="px-4 py-2 bg-orange-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-colors">
                            Có, chia sẻ
                        </button>
                        <button id="location-no" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                            Không, chọn ngẫu nhiên
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>

    <script>
    function weatherSlideshow() {
        return {
            autoPlayInterval: null,
            
            init() {
                // Không tự động lấy vị trí khi khởi tạo
                console.log('WeatherSlideshow initialized');
                this.startAutoPlay();
            },

            startAutoPlay() {
                // Clear any existing interval
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                }
                
                // Start auto-advance if enabled and there are multiple slides
                this.autoPlayInterval = setInterval(() => {
                    @this.autoAdvanceSlide();
                }, 5000); // 5 seconds
            },

            stopAutoPlay() {
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                    this.autoPlayInterval = null;
                }
            },

            handleKeyboard(event) {
                if (event.key === 'ArrowLeft') {
                    @this.previousSlide();
                } else if (event.key === 'ArrowRight') {
                    @this.nextSlide();
                } else if (event.key === ' ') {
                    event.preventDefault();
                    @this.toggleAutoPlay();
                }
            },
            
            getUserLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            console.log('Đã lấy được vị trí:', latitude, longitude);
                            console.log('Tọa độ đã lấy được, nhưng không thể gửi về Livewire');
                        },
                        (error) => {
                            console.log('Lỗi lấy vị trí:', error.message);
                            alert('Không thể lấy vị trí: ' + error.message);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                } else {
                    alert('Trình duyệt không hỗ trợ lấy vị trí');
                }
            }
        }
    }

    document.addEventListener('livewire:init', () => {
        // Hàm hiển thị modal
        function showLocationModal() {
            const modal = document.getElementById('location-modal');
            modal.classList.remove('hidden');
        }
        
        // Hàm ẩn modal
        function hideLocationModal() {
            const modal = document.getElementById('location-modal');
            modal.classList.add('hidden');
        }
        
        // Xử lý sự kiện click nút trong modal
        document.getElementById('location-yes').addEventListener('click', function() {
            hideLocationModal();
            // Lấy vị trí
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        console.log('Đã lấy được vị trí:', latitude, longitude);
                        
                        // Gửi tọa độ về Livewire
                        @this.setUserLocation(latitude, longitude);
                    },
                    (error) => {
                        console.log('Lỗi lấy vị trí:', error.message);
                        
                        // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                        if (error.code === 1) { // PERMISSION_DENIED
                            console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                            @this.randomCity();
                        } else {
                            alert('Không thể lấy vị trí: ' + error.message);
                            @this.randomCity();
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Trình duyệt không hỗ trợ lấy vị trí');
                @this.randomCity();
            }
        });
        
        document.getElementById('location-no').addEventListener('click', function() {
            hideLocationModal();
            // Chọn ngẫu nhiên
            console.log('Người dùng không muốn chia sẻ vị trí, chọn ngẫu nhiên...');
            @this.randomCity();
        });

       
        Livewire.on('auto-get-location', () => {
            showLocationModal();
        });

        Livewire.on('get-user-location', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        console.log('Đã lấy được vị trí:', latitude, longitude);
                        
                      
                        @this.setUserLocation(latitude, longitude);
                    },
                    (error) => {
                        console.log('Lỗi lấy vị trí:', error.message);
                       
                        if (error.code === 1) { // PERMISSION_DENIED
                            console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                            @this.randomCity();
                        } else {
                            alert('Không thể lấy vị trí: ' + error.message);
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Trình duyệt không hỗ trợ lấy vị trí');
            }
        });

       
        Livewire.on('autoplay-changed', (event) => {
            const slideshowComponent = document.querySelector('[x-data*="weatherSlideshow"]').__x?.$data;
            if (slideshowComponent) {
                if (event.autoPlay) {
                    slideshowComponent.startAutoPlay();
                } else {
                    slideshowComponent.stopAutoPlay();
                }
            }
        });

        Livewire.on('alert', (event) => {
            alert(event.message);
        });
    });
    </script>
</div> 