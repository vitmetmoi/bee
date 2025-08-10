<div class="py-12 mt-[5%] bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Container with White Background -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-br from-orange-100 to-red-100 text-black px-8 py-6">
                <div class="text-center">
                    <h2 class="text-3xl text-black font-bold mb-2">
                        🌤️ Món Ăn Phù Hợp Với Thời Tiết
                    </h2>
                    <p class="text-lg opacity-90 max-w-2xl mx-auto">
                        Khám phá những món ăn ngon được đề xuất theo thời tiết hiện tại
                    </p>
                </div>
                
                @if(!$nearestCity)
                    <!-- Nút lấy vị trí -->
                    <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3 max-w-md mx-auto">
                        <div class="text-center space-y-3">
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button onclick="showLocationModal()" 
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
                @else
                    <!-- Hiển thị thông tin thành phố hiện tại -->
                    <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3 max-w-md mx-auto">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Thành phố hiện tại: {{ $nearestCity->name }}</span>
                            </div>
                            @if(session('user_location.is_random'))
                                <p class="text-xs opacity-75">Được chọn ngẫu nhiên</p>
                            @else
                                <p class="text-xs opacity-75">Dựa trên vị trí của bạn</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="p-8">
                @if(count($recipes) > 0)
                    <!-- Weather Info Card -->
                    <div class="bg-white rounded-xl p-6 mb-8 border border-gray-100 shadow-sm">
                        <div class="flex flex-col lg:flex-row items-center justify-between">
                            <div class="flex items-center mb-4 lg:mb-0">
                                <div class="mr-4">
                                    <!-- City Selector -->
                                    <div class="relative">
                                        <select wire:model.live="selectedCity" wire:change="changeCity($event.target.value)" 
                                                class="appearance-none bg-transparent border-none text-xl font-semibold text-gray-900 focus:outline-none focus:ring-0 cursor-pointer">
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
                                    </div>
                                    @if($weatherData)
                                        <p class="text-sm text-gray-600 mt-1">{{ $weatherData['weather_description'] ?? 'Thời tiết đẹp' }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-8">
                                @if($weatherData)
                                    <div class="text-center">
                                        <p class="text-3xl font-bold text-blue-500">
                                            {{ number_format($weatherData['temperature'], 1) }}°C
                                        </p>
                                        <p class="text-sm text-gray-600">Nhiệt độ</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-3xl font-bold text-blue-500">{{ $weatherData['humidity'] }}%</p>
                                        <p class="text-sm text-gray-600">Độ ẩm</p>
                                    </div>
                                @endif
                                <div class="text-center">
                                    <p class="text-sm text-gray-800 bg-gradient-to-r from-orange-100 to-red-100 px-4 py-2 rounded-full shadow-sm border border-orange-200">
                                        Món phù hợp với thời tiết
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slideshow Container -->
                    <div class="relative bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <!-- Slides -->
                        <div class="relative h-96">
                            @foreach($recipes as $index => $recipe)
                                <div class="absolute inset-0 transition-opacity duration-500 ease-in-out {{ $index === $currentSlide ? 'opacity-100' : 'opacity-0' }}"
                                     id="slide-{{ $index }}">
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
                                                    Món {{ $index + 1 }}/{{ count($recipes) }}
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
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                    {{ number_format($recipe->average_rating, 1) }}
                                                </div>
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
                        <button wire:click="previousSlide" 
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <button wire:click="nextSlide" 
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <!-- Dots Indicator -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                            @foreach($recipes as $index => $recipe)
                                <button wire:click="goToSlide({{ $index }})" 
                                        class="w-3 h-3 rounded-full transition-all duration-200 {{ $index === $currentSlide ? 'bg-orange-500' : 'bg-gray-300 hover:bg-gray-400' }}">
                                </button>
                            @endforeach
                        </div>
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
    

    
    <style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>

    <script>
    // Hàm hiển thị modal chia sẻ vị trí với SweetAlert
    function showLocationModal() {
        Swal.fire({
            title: 'Chia sẻ vị trí',
            text: 'Bạn có muốn chia sẻ vị trí hiện tại để nhận đề xuất món ăn phù hợp với thời tiết không?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Có, chia sẻ',
            cancelButtonText: 'Không, chọn ngẫu nhiên',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'rounded-md',
                cancelButton: 'rounded-md'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Lấy vị trí
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            console.log('Đã lấy được vị trí:', latitude, longitude);
                            
                            // Hiển thị thông báo thành công
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã lấy được vị trí của bạn',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Gửi tọa độ về Livewire
                            @this.setUserLocation(latitude, longitude);
                        },
                        (error) => {
                            console.log('Lỗi lấy vị trí:', error.message);
                            
                            // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                            if (error.code === 1) { // PERMISSION_DENIED
                                console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                                Swal.fire({
                                    title: 'Đã chọn ngẫu nhiên',
                                    text: 'Sẽ hiển thị món ăn phù hợp với thời tiết ngẫu nhiên',
                                    icon: 'info',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                @this.randomCity();
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: 'Không thể lấy vị trí: ' + error.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
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
                    Swal.fire({
                        title: 'Không hỗ trợ',
                        text: 'Trình duyệt không hỗ trợ lấy vị trí',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    @this.randomCity();
                }
            } else {
                // Chọn ngẫu nhiên
                console.log('Người dùng không muốn chia sẻ vị trí, chọn ngẫu nhiên...');
                Swal.fire({
                    title: 'Đã chọn ngẫu nhiên',
                    text: 'Sẽ hiển thị món ăn phù hợp với thời tiết ngẫu nhiên',
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false
                });
                @this.randomCity();
            }
        });
    }

    document.addEventListener('livewire:init', () => {
        // Tự động lấy vị trí khi component được load
        Livewire.on('auto-get-location', () => {
            showLocationModal();
        });

        // Xử lý khi người dùng click nút lấy vị trí thủ công
        Livewire.on('get-user-location', () => {
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
    });
    </script>
</div> 