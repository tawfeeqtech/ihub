<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $workspace->getTranslatedNameAttribute() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .nav-active { @apply bg-green-800 text-white; }
        .menu-toggle span { @apply bg-green-700; }
        .menu-toggle.active span:nth-child(1) { @apply rotate-45 translate-x-1 translate-y-1; }
        .menu-toggle.active span:nth-child(2) { @apply opacity-0; }
        .menu-toggle.active span:nth-child(3) { @apply -rotate-45 translate-x-1 -translate-y-1; }
    </style>
</head>
<body class="font-sans text-gray-800">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white bg-opacity-90 backdrop-blur-md shadow-md z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-green-700">IHubs</a>
            <div class="md:hidden">
                <button class="menu-toggle focus:outline-none">
                    <span class="block w-6 h-0.5 mb-1"></span>
                    <span class="block w-6 h-0.5 mb-1"></span>
                    <span class="block w-6 h-0.5"></span>
                </button>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="#" class="text-green-700 hover:text-green-900">{{ __('Home') }}</a>
                <a href="#" class="text-green-700 hover:text-green-900">{{ __('Features') }}</a>
                <a href="#" class="text-green-700 hover:text-green-900">{{ __('Vision') }}</a>
                <a href="#" class="text-green-700 hover:text-green-900">{{ __('About Us') }}</a>
            </div>
        </div>
        <div class="nav-links hidden md:hidden absolute top-full left-0 w-full bg-white bg-opacity-95 backdrop-blur-md">
            <a href="#" class="block px-4 py-2 text-green-700 hover:bg-green-100">{{ __('Home') }}</a>
            <a href="#" class="block px-4 py-2 text-green-700 hover:bg-green-100">{{ __('Features') }}</a>
            <a href="#" class="block px-4 py-2 text-green-700 hover:bg-green-100">{{ __('Vision') }}</a>
            <a href="#" class="block px-4 py-2 text-green-700 hover:bg-green-100">{{ __('About Us') }}</a>
        </div>
    </nav>

    <!-- Workspace Details -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-green-700 mb-6">{{ $workspace->getTranslatedNameAttribute() }}</h1>

            <!-- Images -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @forelse ($workspace->images as $image)
                    <img src="{{ Storage::url($image->image) }}" alt="{{ $workspace->getTranslatedNameAttribute() }}" class="w-full h-64 object-cover rounded-lg shadow-md">
                @empty
                    <p class="text-center text-gray-600">{{ __('No images available.') }}</p>
                @endforelse
            </div>

            <!-- Description -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-green-700 mb-2">{{ __('Description') }}</h2>
                <p class="text-gray-600">{{ $workspace->description[app()->getLocale()] ?? $workspace->description['ar'] }}</p>
            </div>

            <!-- Location -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-green-700 mb-2">{{ __('Location') }}</h2>
                <p class="text-gray-600">
                    {{ $workspace->governorate->translated_name }} / {{ $workspace->region->translated_name }} / {{ $workspace->location[app()->getLocale()] ?? $workspace->location['ar'] }}
                </p>
            </div>

            <!-- Features -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-green-700 mb-2">{{ __('Features') }}</h2>
                <ul class="list-disc list-inside text-gray-600">
                    @foreach ($workspace->features as $feature)
                        <li>{{ $feature[app()->getLocale()] ?? $feature['ar'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-green-900 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p>{{ __('© 2025 IHubs. All rights reserved.') }}</p>
        </div>
    </footer>

    <script>
        const menuToggle = document.querySelector(".menu-toggle");
        const navLinks = document.querySelector(".nav-links");
        if (menuToggle && navLinks) {
            menuToggle.addEventListener("click", () => {
                navLinks.classList.toggle("active");
                menuToggle.classList.toggle("active");
                const isExpanded = navLinks.classList.contains("active");
                menuToggle.setAttribute("aria-expanded", isExpanded);
            });
        }
    </script>
</body>
</html>
