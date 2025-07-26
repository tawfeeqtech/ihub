<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Workspaces') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-sans">
    <!-- Search Filter -->
    <section class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">{{ __('Find Your Workspace') }}</h2>
            <form action="{{ route('workspaces') }}" method="GET" class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium">{{ __('Workspace Name') }}</label>
                        <input type="text" name="name" id="name" value="{{ request('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="governorate_id" class="block text-sm font-medium">{{ __('Governorate') }}</label>
                        <select name="governorate_id" id="governorate_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="updateRegions(this)">
                            <option value="">{{ __('Select Governorate') }}</option>
                            @foreach ($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>{{ $governorate->translated_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="region_id" class="block text-sm font-medium">{{ __('Region') }}</label>
                        <select name="region_id" id="region_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                            <option value="">{{ __('Select Region') }}</option>
                            @if (request('governorate_id'))
                                @foreach (\App\Models\Region::where('governorate_id', request('governorate_id'))->get() as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->translated_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="has_free" value="1" id="has_free" class="h-4 w-4 text-blue-600" {{ request('has_free') ? 'checked' : '' }}>
                        <label for="has_free" class="ml-2 text-sm">{{ __('Free Workspace') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="has_evening_shift" value="1" id="has_evening_shift" class="h-4 w-4 text-blue-600" {{ request('has_evening_shift') ? 'checked' : '' }}>
                        <label for="has_evening_shift" class="ml-2 text-sm">{{ __('Evening Shift') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="bank_payment_supported" value="1" id="bank_payment_supported" class="h-4 w-4 text-blue-600" {{ request('bank_payment_supported') ? 'checked' : '' }}>
                        <label for="bank_payment_supported" class="ml-2 text-sm">{{ __('Bank Payment Supported') }}</label>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">{{ __('Search') }}</button>
            </form>
        </div>
    </section>

    <!-- Workspaces Grid -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($workspaces as $workspace)
                    <a href="{{ route('workspaces.show', $workspace) }}" class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <img src="{{ $workspace->images->first() ? Storage::url($workspace->images->first()->image) : 'https://via.placeholder.com/300' }}" alt="{{ $workspace->getTranslatedNameAttribute() }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold">{{ $workspace->getTranslatedNameAttribute() }}</h3>
                            <p class="text-gray-600">{{ $workspace->location[app()->getLocale()] ?? $workspace->location['ar'] }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-center col-span-3">{{ __('No workspaces found.') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>{{ __('&copy; 2025 Your Company. All rights reserved.') }}</p>
        </div>
    </footer>

    <!-- JavaScript for Dynamic Region Loading -->
    <script>
        function updateRegions(select) {
            const governorateId = select.value;
            const regionSelect = document.getElementById('region_id');
            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">{{ __('Select Region') }}</option>';

            if (governorateId) {
                fetch(`/api/regions?governorate_id=${governorateId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(region => {
                            const option = document.createElement('option');
                            option.value = region.id;
                            option.textContent = region.translated_name;
                            regionSelect.appendChild(option);
                        });
                        regionSelect.disabled = false;
                    });
            }
        }
    </script>
</body>
</html>
