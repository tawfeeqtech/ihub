<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Home') }}</title>
      <link rel="stylesheet" href="{{ asset('ihub/Fonts/fontawesome/css/all.min.css') }}" />

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('ihub/style.css') }}">
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #215245 0%, #2d6c5b 100%), url('https://source.unsplash.com/random/1920x1080/?office');
            background-blend-mode: overlay;
            background-size: cover;
            background-position: center;
        }
        .nav-active { @apply bg-green-800 text-white; }
        .menu-toggle span { @apply bg-green-700; }
        .menu-toggle.active span:nth-child(1) { @apply rotate-45 translate-x-1 translate-y-1; }
        .menu-toggle.active span:nth-child(2) { @apply opacity-0; }
        .menu-toggle.active span:nth-child(3) { @apply -rotate-45 translate-x-1 -translate-y-1; }
    </style>
</head>
<body class="font-sans text-gray-800">
<nav class="navbar">
  <div class="container">
    <div class="nav-content">
      <a href="/" class="logo"><img src="{{ asset('ihub/Image/Asset 21.svg') }}" alt="Logo" /></a>

      <div class="nav-links">
        <a href="#workspaces">{{ __('Workspaces') }}</a>
        <a href="#features">{{ __('Features') }}</a>
        <a href="#vision">{{ __('Vision') }}</a>
        <a href="#about">{{ __('About Us') }}</a>
      </div>

      <div class="nav-right">
        <div class="language-switcher">
          @if(app()->getLocale() == 'en')
            <span>English</span>
          @else
            <a href="{{ route('language.switcher', 'en') }}">English</a>
          @endif

          <div class="lang-separator"></div>

          @if(app()->getLocale() == 'ar')
            <span>العربية</span>
          @else
            <a href="{{ route('language.switcher', 'ar') }}">العربية</a>
          @endif
        </div>
        <button class="menu-toggle" aria-label="Toggle menu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>

    </div>
  </div>
</nav>
    <!-- Hero Section -->
    {{-- <section class="hero-bg min-h-screen flex items-center pt-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ __('Discover Your Perfect Workspace') }}</h1>
            <p class="text-lg mb-8">{{ __('Explore and manage workspaces with ease') }}</p>
            <a href="{{ route('workspaces') }}" class="inline-block bg-green-700 text-white px-6 py-3 rounded-full hover:bg-green-800">{{ __('Explore Workspaces') }}</a>

            <!-- Search Filter -->
            <form action="{{ route('workspaces') }}" method="GET" class="bg-white bg-opacity-90 p-6 rounded-lg shadow-lg mt-8 max-w-2xl mx-auto text-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium">{{ __('Workspace Name') }}</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="governorate_id" class="block text-sm font-medium">{{ __('Governorate') }}</label>
                        <select name="governorate_id" id="governorate_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="updateRegions(this)">
                            <option value="">{{ __('Select Governorate') }}</option>
                            @foreach ($governorates as $governorate)
                                <option value="{{ $governorate->id }}">{{ $governorate->translated_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="region_id" class="block text-sm font-medium">{{ __('Region') }}</label>
                        <select name="region_id" id="region_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                            <option value="">{{ __('Select Region') }}</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="has_free" value="1" id="has_free" class="h-4 w-4 text-green-600">
                        <label for="has_free" class="ml-2 text-sm">{{ __('Free Workspace') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="has_evening_shift" value="1" id="has_evening_shift" class="h-4 w-4 text-green-600">
                        <label for="has_evening_shift" class="ml-2 text-sm">{{ __('Evening Shift') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="bank_payment_supported" value="1" id="bank_payment_supported" class="h-4 w-4 text-green-600">
                        <label for="bank_payment_supported" class="ml-2 text-sm">{{ __('Bank Payment Supported') }}</label>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800">{{ __('Search') }}</button>
            </form>
        </div>
    </section> --}}

    <section class="hero" id="hero">
        <div class="container">
            <div class="hero-content">
            <div class="hero-text">
                <h1>{{ __('Discover Your Perfect Workspace') }}</h1>

                <p>
                {{ __('Discover Your Perfect Workspace description') }}
                </p>

                <div class="hero-cta-container">
                <a href="{{ route('workspaces') }}" class="cta-button">
                    {{ __('Explore Workspaces') }}
                </a>
                </div>
            </div>

            <div class="oval-wrapper">
                <div class="star-ring">
                <span class="star">✦</span> <span class="star">✦</span> <span class="star">✦</span>
                <span class="star">✦</span> <span class="star">✦</span> <span class="star">✦</span>
                <span class="star">✦</span> <span class="star">✦</span>
                </div>
                <div class="oval-container">
                <img src="{{ asset('ihub/Image/65.png') }}" alt="Door with green frame" />
                </div>
            </div>
            </div>
        </div>
    </section>

    <section class="filter-section">
        <div class="container">
            <div class="section-header">
            <h2>{{ __('Find the Perfect Space') }}</h2>
            <p>{{ __('Find the Perfect section-header') }}</p>
            </div>

            <form action="{{ route('workspaces') }}" method="GET" class="workspace-filter-form">
            <div class="filter-grid">
                <div class="filter-group">
                <input type="text" name="name" id="name" placeholder="{{ __('filter-group name') }}">
                </div>
                <div class="filter-group">
                <select name="governorate_id" id="governorate_id" onchange="updateRegions(this)">
                    <option value="">{{ __('Select Governorate') }}</option>
                    @foreach ($governorates as $governorate)
                    <option value="{{ $governorate->id }}">{{ $governorate->translated_name }}</option>
                    @endforeach
                </select>
                </div>
                <div class="filter-group">
                <select name="region_id" id="region_id" disabled>
                    <option value="">{{ __('Select Region') }}</option>
                </select>
                </div>
                <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="checkbox-grid">
                <div class="checkbox-group">
                <input type="checkbox" name="has_free" value="1" id="has_free_filter">
                <label for="has_free_filter">{{ __('Free Workspace') }}</label>
                </div>
                <div class="checkbox-group">
                <input type="checkbox" name="has_evening_shift" value="1" id="has_evening_shift_filter">
                <label for="has_evening_shift_filter">{{ __('Evening Shift') }}</label>
                </div>
                <div class="checkbox-group">
                <input type="checkbox" name="bank_payment_supported" value="1" id="bank_payment_supported_filter">
                <label for="bank_payment_supported_filter">{{ __('Bank Payment Supported') }}</label>
                </div>
            </div>
            </form>
        </div>
    </section>

    <!-- Latest Workspaces -->
    <section id='workspaces' class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-green-700 mb-8">{{ __('Latest Workspaces') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($latestWorkspaces as $workspace)
                    <a href="{{ route('workspaces.show', $workspace) }}" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <img src="{{ $workspace->images->first() ? Storage::url($workspace->images->first()->image) : 'https://via.placeholder.com/300' }}" alt="{{ $workspace->getTranslatedNameAttribute() }}" class="w-full h-48 object-cover">
                        <div class="p-4 text-center">
                            <h3 class="text-xl font-semibold text-green-700">{{ $workspace->getTranslatedNameAttribute() }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('View More') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Download App Section -->
    <section class="py-12 bg-green-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-green-700 mb-4">{{ __('Download Our App') }}</h2>
            <p class="text-gray-600 mb-6">{{ __('Manage workspaces on the go') }}</p>
            <div class="flex justify-center space-x-4">
                <a href="#" class="inline-block bg-white p-2 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <img src="{{ asset('uploads/GooglePlay.png') }}" alt="Google Play" class="h-10">
                </a>
                <a href="#" class="inline-block bg-white p-2 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <img src="{{ asset('uploads/AppStore.png') }}" alt="App Store" class="h-10">
                </a>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">{{ __('Our Amazing Features') }}</h2>
            <div class="features-content">
                <div class="features-mockup">
                    <img
                        src="{{ asset('ihub/Image/65.png') }}"
                        alt="Mobile App Mockup showcasing features"
                    />
                </div>
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-th-large feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('Manage Workspaces') }}</h3>
                            <p>{{ __('Manage Workspaces description') }}
                            </p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-hourglass-half feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('Time Management and Organization') }}</h3>
                            <p>{{ __('Time Management and Organization description') }}

                            </p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-concierge-bell feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('Easily Meeting Needs') }}</h3>
                            <p>{{ __('Easily Meeting Needs description') }}

                            </p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-box-open feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('Package Discovery') }}</h3>
                            <p>{{ __('Package Discovery description') }}

                            </p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-user-cog feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('Workspace Management') }}</h3>
                            <p>{{ __('Workspace Management description') }}

                            </p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-container">
                            <i class="fas fa-layer-group feature-icon"></i>
                        </div>
                        <div class="feature-text-content">
                            <h3>{{ __('An Ideal Operating System') }}</h3>
                            <p>{{ __('An Ideal Operating System description') }}

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="vision" class="vision-section" >
        <div class="container">
        <h2 class="section-title">{{ __('Our Vision') }}</h2>
        <div class="vision-content">
            <div class="vision-text-block">
            <h3>{{ __('Smarter Workspaces. Better Productivity') }}</h3>
            <p>{{ __('Our Vision description1') }}</p>
            <p>{{ __('Our Vision description2') }}</p>
              <a href="#" class="btn-primary">{{ __('Learn More About Our Mission') }}</a>
            </div>
            <div class="vision-image-gallery">
            <div class="gallery-item gallery-item-large">
                <img
                src="{{ asset('ihub/Image/vision-1.png') }}"
                alt="Team working together, representing collaboration"
                />
            </div>
            <div class="gallery-item gallery-item-small">
                <img
                src="{{ asset('ihub/Image/vision-2.png') }}"
                alt="Abstract data visualization, representing insights"
                />
            </div>
            <div class="gallery-item gallery-item-small">
                <img
                src="{{ asset('ihub/Image/vision-3.png') }}"
                alt="Developer coding on a screen, representing innovation"
                />
            </div>
            </div>
        </div>
        </div>
    </section>

    <section id="about" class="about-section" >
        <div class="container">
        <h2 class="section-title">{{ __('About Us') }}</h2>
        <div class="about-content">
            <div class="about-image-block">
            <img
                src="{{ asset('ihub/Image/about-us-team.png') }}"
                alt="Our dedicated team working together"/>
            <div class="about-stats">
              <div class="stat-item">
                <i class="fas fa-users-gear stat-icon"></i>
                <span class="stat-number">50+</span>
                <span class="stat-label">Team Members</span>
              </div>
              <div class="stat-item">
                <i class="fas fa-award stat-icon"></i>
                <span class="stat-number">10+</span>
                <span class="stat-label">Years Experience</span>
              </div>
              <div class="stat-item">
                <i class="fas fa-code stat-icon"></i>
                <span class="stat-number">100k+</span>
                <span class="stat-label">Lines of Code</span>
              </div>
            </div>
            <div class="social-links-about">
              <a
                href="https://www.instagram.com/ghayatech/"
                target="_blank"
                aria-label="Follow us on Instagram">
                <i class="fab fa-instagram social-icon"></i>
              </a>
            </div>
          </div>
          <div class="about-text-block">
            <h3>{{ __('Who We Are: Innovators at Heart') }}</h3>
            <p>
              {{ __('About Us description1') }}
            </p>
            <p>
              {{ __('About Us description2') }}
            </p>
            <div class="mission-statement">
              <h4>{{ __('Our Mission') }}</h4>
              <p>
                {{ __('Our Mission description') }}
              </p>
            </div>
            <a href="https://ghayatech.com/" target="_blank" class="btn-primary">{{ __('Meet Our Team') }}</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
      <div class="container">
        <p>&copy; <span id="current-year"></span> {{ __('Powerd by') }} <a href="https://ghayatech.com/" target="_blank">GhayaTech</a> </p>
      </div>
    </footer>

<script>
  document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('current-year').textContent = new Date().getFullYear();
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinksContainer = document.querySelector('.nav-links');
    const allNavLinks = document.querySelectorAll('.nav-links a');

    // --- Desktop Logic: Handle active link state ---
    allNavLinks.forEach(function(link) {
      link.addEventListener('click', function(event) {
        // 1. Remove .active class from all links
        allNavLinks.forEach(function(navLink) {
          navLink.classList.remove('active');
        });

        // 2. Add .active class to the clicked link
        event.currentTarget.classList.add('active');

        // --- Mobile Logic: Close menu after click ---
        if (navLinksContainer.classList.contains('active')) {
          menuToggle.classList.remove('active');
          navLinksContainer.classList.remove('active');
        }
      });
    });

    // --- Mobile Logic: Toggle menu with hamburger button ---
    menuToggle.addEventListener('click', function () {
      menuToggle.classList.toggle('active');
      navLinksContainer.classList.toggle('active');
    });
  });
</script>
    <script>

        function updateRegions(select) {
            const governorateId = select.value;
            const regionSelect = document.getElementById('region_id');

            regionSelect.disabled = true;
            regionSelect.innerHTML = '<option value="">{{ __('Select Region') }}</option>';
            if (governorateId) {
                fetch(`/api/regions?governorate_id=${governorateId}`, {
                    headers: {
                        'Accept-Language': '{{ app()->getLocale() }}' // إرسال اللغة الحالية
                    }
                })

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
